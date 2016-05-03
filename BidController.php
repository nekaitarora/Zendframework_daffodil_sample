<?php

/**
 * Bid controller for Bid page
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace CsnCms\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Form\Element;
// Pagination
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Paginator\Paginator;
use CsnCms\Entity\Bid;
use CsnCms\Form\BidForm;
use CsnCms\Form\BidFilter;
use CsnCms\Model\Location;
use CsnCms\Model\CompanyType;
use CsnCms\Model\User;
use Zend\Http\Request;
use CsnCms\Model\ActivityModel;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Validator\File\Size;
use Zend\Session\Container;
use CsnCms\Entity\KeyProjectArea;

class BidController extends AbstractActionController {

    /**
     * Index action for bid page
     *
     * @param void
     * @return object
     */
    public function indexAction() {

        $serviceLocator = $this->getServiceLocator();
        $sql = new Sql($serviceLocator->get('Adapter'));
        $select = $sql->select();
        $select->join(array('u' => 'mosaic_dat_user'), 'u.BidID=b.BidID', array(), \Zend\Db\Sql\Select::JOIN_LEFT);
        $columns = array();
        $columns[] = new \Zend\Db\Sql\Expression('(select u.FirstName from mosaic_dat_user as u where u.BidID=b.BidID and u.RoleID=3 order by u.UserID DESC LIMIT 1) AS FirstName');
        $columns[] = new \Zend\Db\Sql\Expression('(select u.LastName from mosaic_dat_user as u where u.BidID=b.BidID and u.RoleID=3 order by u.UserID DESC LIMIT 1) AS LastName');
        $columns[] = new \Zend\Db\Sql\Expression('(select count(c.CompanyID) as count from mosaic_dat_company as c where c.BidID=b.BidID) AS Count');
        $columns[] = 'Name';
        $columns[] = 'BidID';
        $columns[] = 'DateModified';
        $select->columns($columns);
        $select->from(array('b' => 'mosaic_dat_bid'));

        if (isset($_GET['searchkeyword']) && $_GET['searchkeyword'] != '') {

            $where = new Where();
            $where
                    ->nest()
                    ->like('Name', '%' . $_GET['searchkeyword'] . '%')
                    ->or
                    ->like('u.FirstName', '%' . $_GET['searchkeyword'] . '%')
                    ->or
                    ->like('u.LastName', '%' . $_GET['searchkeyword'] . '%');
            $select->where($where);
        }

        $select->group(array('b' => 'BidID'));
        $adapter = new DbSelect($select, $serviceLocator->get('Adapter'));
        $paginator = new Paginator($adapter);

        $page = 1;
        if ($this->params()->fromRoute('page'))
            $page = $this->params()->fromRoute('page');

        $paginator->setCurrentPageNumber((int) $page);
// set the number of items per page to 10
        $paginator->setItemCountPerPage(25);

        return new ViewModel(array('bids' => $paginator));
    }

    /**
     * Action to delete levy band
     *
     * @param void
     * @return json
     */
    public function deletebandAction() {
        $serviceLocator = $this->getServiceLocator();
        $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');
        $id = $_POST['id'];
        if (isset($id) && $id != '') {
            try {
                $band = $entityManager->getRepository('\CsnCms\Entity\RvValue')->find($id);
                $entityManager->remove($band);
                $entityManager->flush();
            } catch (\Exception $e) {
                
            }
            echo "1";
            exit;
        }
        echo "0";
        exit;
    }

    /**
     * Add action for bid page
     *
     * @param void
     * @return object
     */
    public function addAction() {
        $id = (int) $this->params()->fromRoute('id', 0);

        $serviceLocator = $this->getServiceLocator();

        $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');

// Find bid detail
        $bid = $entityManager->find('CsnCms\Entity\Bid', $id);

// Get Form Mode
        $mode = $this->getMode($bid);

        if (!$bid instanceof Bid) {
            $bid = new Bid();
        } else {
            
        }

// Get User list based on bid id
        $userList = $this->getUserList($id, $serviceLocator);

// Get form object
        $form = $this->formObject($serviceLocator, $id, $bid);

// Get request object
        $request = $this->getRequest();

        $success = false;

// Get user identity
        $userData = $this->identity();

// Check forr post form data
        $success = $this->postForm($request, $form, $bid, $id, $entityManager, $userData);

// Check success for form post successfully or not
        if (!is_object($success) && (int) $success == 2) {

// Call activity model
            $activity = new ActivityModel();

            $type = ($id == 0 ? 'Added' : 'Updated');
// Save activity data
            $activity->saveActivity($entityManager, addslashes($bid->getName()), $type, 'Bid', new \DateTime(date('Y-m-d h:i:s')), addslashes($userData->getUsername()));

            return $this->redirect()->toRoute('admin/default', array('controller' => 'bid', 'action' => 'import'));
        } else if (!is_object($success) && $success == true) {

// Call activity model
            $activity = new ActivityModel();

            $type = ($id == 0 ? 'Added' : 'Updated');
// Save activity data
            $activity->saveActivity($entityManager, addslashes($bid->getName()), $type, 'Bid', new \DateTime(date('Y-m-d h:i:s')), addslashes($userData->getUsername()));
            return $this->redirect()->toRoute('admin/default', array('controller' => 'bid', 'action' => 'view', 'id' => $bid->getBidid()));
        } else {
            $form = $success;
        }
        $bidid = $bid->getBidid();
        $bands = '';
        $levRes = '';
        if (isset($bidid) && $bidid != '') {
            $dql2 = "SELECT u FROM CsnCms\Entity\RvValue u where u.bidid=$bidid";
            $query = $entityManager->createQuery($dql2);
            $bands = $query->getResult();
            $dql2 = "SELECT u FROM CsnCms\Entity\Control u where u.bidid=$bidid and u.type='RV'";
            $query = $entityManager->createQuery($dql2);
            $levRes = $query->getResult();
        }
        return new ViewModel(array('form' => $form, 'mode' => $mode, 'id' => $id, 'userList' => $userList, 'bands' => $bands, 'levRes' => $levRes, 'bid' => $bid));
    }

    /**
     * Method to get form object
     *
     * @param object,number,number
     * @return object
     */
    public function formObject($serviceLocator, $id, $bid) {

        $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');

// Get user list
        $this->getUserList($id, $serviceLocator);

// Get Form Mode
        $mode = $this->getMode($bid);

// Create an object for form        
        $form = new BidForm($entityManager);

        $form->setInputFilter(new \CsnCms\Form\BidFilter());

// assign hydrator
        $hydrator = new DoctrineHydrator($entityManager, get_class($bid));
        $form->setHydrator($hydrator);

// bind object to form
        $form->bind($bid);

        return $form;
    }

    /**
     * Method to get userlist
     *
     * @param number,object
     * @return object
     */
    public function getUserList($id, $serviceLocator, $userId = '') {
        $userList = array();
        if ($id != 0) {
// Create object for contact model
            $user = new User();

// Set adapter object for accessing database
            $user->setDbAdapter($serviceLocator->get('Adapter'));
// Access State list
            if ($userId != '') {
                $userList = $user->bidUserList($id, $userId);
            } else {
                $userList = $user->bidUserList($id);
            }
        }

        return $userList;
    }

    /**
     * Method to get mode
     *
     * @param number
     * @return string
     */
    public function getMode($bid) {

        $mode = "Edit BID";
        if (!$bid instanceof Bid) {
            $bid = new Bid();
            $mode = "New BID";
        }
        return $mode;
    }

    /**
     * Method to post form
     *
     * @param object,object,number,number,object,object
     * @return boolean or object
     */
    public function postForm($request, $form, $bid, $id, $entityManager, $userData, $File = '') {

// Check for post data
        if ($request->isPost()) {
            $data = $request->getPost();

            $imageFileName = $bid->getBidmap();
            $nonFile = $request->getPost()->toArray();
            $biddata = '';
            $File = '';
            if (!is_array($File)) {
                $File = $this->params()->fromFiles('bidmap');
                $biddata = $this->params()->fromFiles('biddata');
            }

            $data = array_merge(
                    $nonFile, array('bidmap' => $File), array('biddata' => '')
            );

            $form->setData($data);


            if ($form->isValid($request)) {

                $size = new Size(array('min' => 200)); //minimum bytes filesize

                $adapter = new \Zend\File\Transfer\Adapter\Http();
                $adapter->setValidators(array($size), $File['name']);

                //if (!$adapter->isValid() && $File['name'] != '') {
                if (false) {
                    $dataError = $adapter->getMessages();
                    $error = array();
                    foreach ($dataError as $key => $row) {
                        $error[] = $row;
                    }
                    $form->setMessages(array('fileupload' => $error));

                    return $form;
                } else {

                    if (isset($File['name']) && $File['name'] != '') {
                        $adapter->setDestination(FILE_PATH . '/public/img/bid');
                        $adapter->receive($File['name']);
                    }

                    if (isset($biddata['name']) && $biddata['name'] != '') {
                        $adapterData = new \Zend\File\Transfer\Adapter\Http();
                        $adapterData->setDestination(FILE_PATH . '/public/excel');
                        $adapterData->receive($biddata['name']);
//print_r($adapterData->receive($biddata['name']));
                    }

                    if ($adapter->receive($File['name']) || $File['name'] == '') {
                        $bid->setBidMap($File['name']);
                        if ($id == 0) {

//$bid->setProjectarea(implode(',',$data['projectarea']));
                            $bid->setDateCreated(new \DateTime(date('Y-m-d h:i:s')));
                            $bid->setDateModified(new \DateTime(date('Y-m-d h:i:s')));
                            $bid->setUserCreated($userData->getId());
                            $bid->setUserModified($userData->getId());
                            $entityManager->persist($bid);
                            $entityManager->flush();
                            $this->flashMessenger()->addMessage('Bid Successfully Added');
                        } else {
                            if (isset($data['projectarea']) && is_array($data['projectarea'])) {
                                $bid->setProjectarea(implode(',', $data['projectarea']));
                            } else {
                                $bid->setProjectarea('');
                            }
                            if ($File['name'] == '') {
                                $bid->setBidmap($imageFileName);
                            }
                            $bid->setDatemodified(new \DateTime(date('Y-m-d h:i:s')));
                            $bid->setUserModified($userData->getId());
                            $entityManager->flush();

                            $this->flashMessenger()->addMessage('Bid Successfully Changed');
                        }
                        $levyType = $_POST['levytype'];
                        if ($levyType == 'Banded') {
                            $val = $_POST['value'];
                            $fromvalue = $_POST['fromvalue'];
                            $tovalue = $_POST['tovalue'];
                            $bandid = isset($_POST['bandid']) ? $_POST['bandid'] : '';
                            $unlimited = isset($_POST['unlimited']) ? $_POST['unlimited'] : 0;
                            $countfrom = count($fromvalue);
                            if ($countfrom > 0 && $fromvalue[0] != '') {
                                foreach ($fromvalue as $k => $v) {
                                    if (isset($bandid[$k]) && $bandid[$k] != '') {
                                        $band = $entityManager->getRepository('\CsnCms\Entity\RvValue')->find($bandid[$k]);
                                    } else {
                                        $band = new \CsnCms\Entity\RvValue();
                                    }
                                    $band->setFromvalue($fromvalue[$k]);
                                    $band->setValue($val[$k]);
                                    $band->setBidid($bid->getBidid());
                                    $band->setUsercreated(1);
                                    $band->setUsermodified(1);
                                    if ($unlimited != 0 && $k == $countfrom - 1) {
                                        $band->setUnlimited(1);
                                        $band->setTovalue(0);
                                    } else {
                                        $band->setUnlimited(0);
                                        $band->setTovalue($tovalue[$k]);
                                    }
                                    $date = new \DateTime(date('Y-m-d h:i:s'));
                                    $band->setDatecreated($date);
                                    $band->setDatemodified($date);
                                    $band->setIpaddress("127.0.0.1");
                                    $entityManager->persist($band);
                                    $entityManager->flush();
                                }
                            }
                        } else if ($levyType == 'Percentage Rate') {
                            if (isset($_POST['levyPerID']) && $_POST['levyPerID'] != '') {
                                $levy = $entityManager->getRepository('\CsnCms\Entity\Control')->find($_POST['levyPerID']);
                            } else {
                                $levy = new \CsnCms\Entity\Control();
                            }
                            $levy->setBidid($bid->getBidid());
                            $date = new \DateTime(date('Y-m-d h:i:s'));
                            $levy->setDatecreated($date);
                            $levy->setDatemodified($date);
                            $levy->setIpaddress("127.0.0.1");
                            $levy->setType("RV");
                            $levy->setUsercreated(1);
                            $levy->setUsermodified(1);
                            $levy->setValue($_POST['PerLevy']);
                            $entityManager->persist($levy);
                            $entityManager->flush();
                        }


                        if (isset($biddata['name']) && $biddata['name'] != '') {
                            $session = new Container('base');
                            $session->offsetSet('ImportBidId', $bid->getBidid());
                            $session->offsetSet('ImportFile', $biddata['name']);
                            return '2';
                        }
                        return true;
                    }
                }
            }
        } else {
            $form->get('projectarea')->setValue(explode(',', $bid->getProjectarea()));
        }

        return $form;
    }

    /**
     * Add action for bid page
     *
     * @param void
     * @return object
     */
    public function viewAction() {

        $bidId = (int) $this->params()->fromRoute('id', 0);

        $session = new Container('base');
        $session->offsetSet('BidId', $bidId);
        $entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $bid = $entityManager->find('CsnCms\Entity\Bid', $bidId);

        $session->offsetSet('bidName', $bid->getName());

// Create object for contact model
        $user = new User();

// Set adapter object for accessing database
        $user->setDbAdapter($this->getServiceLocator()->get('Adapter'));
// Access State list
        $userList = $user->bidUserList($bidId);

        $location = new Location();
        $location->setDbAdapter($this->getServiceLocator()->get('Adapter'));
        $locationArray = $location->getLocation($bid->getLocationid()->getLocationid());
        foreach ($locationArray as $locationName) {
            $nameLocation = $locationName['Name'];
        }
        $companyType = new CompanyType();
        $companyType->setDbAdapter($this->getServiceLocator()->get('Adapter'));
        $companyArray = $companyType->getCompanyType($bid->getCompanytypeid()->getCompanytypeid());
        foreach ($companyArray as $companyName) {
            $nameCompany = $companyName['Name'];
        }

        $projectArea = $bid->getProjectarea();
        $projectAreaArray = array();
        if ($projectArea != '') {
            $projectAreaArray = explode(',', $projectArea);
        }

        $finalProjectArea = array();
        foreach ($projectAreaArray as $area) {
            $projectAreaObject = $entityManager->find('CsnCms\Entity\KeyProjectArea', $area);
            if (is_object($projectAreaObject))
                $finalProjectArea[] = $projectAreaObject->getName();
        }
        $finalProjectArea = implode(', ', $finalProjectArea);

// Access contact data
//        $dql = "SELECT a FROM CsnCms\Entity\Bid where id=".$bidId;
//        $query = $entityManager->createQuery($dql);
//        $query->setMaxResults(30);
//        // I will get a collection of Articles
//        $bid = $query->getResult();
        if (isset($bidId) && $bidId != '') {
            $dql2 = "SELECT u FROM CsnCms\Entity\RvValue u where u.bidid=$bidId";
            $query = $entityManager->createQuery($dql2);
            $bands = $query->getResult();
            $dql2 = "SELECT u FROM CsnCms\Entity\Control u where u.bidid=$bidId and u.type='RV'";
            $query = $entityManager->createQuery($dql2);
            $levRes = $query->getResult();
        }

        return new ViewModel(array('bid' => $bid, 'bidId' => $bidId, 'userList' => $userList, 'locationName' => $nameLocation, 'companyName' => $nameCompany, 'finalProjectArea' => $finalProjectArea, 'bands' => $bands, 'levRes' => $levRes));
    }

    /**
     * Get Form object method
     *
     * @param void
     * @return object
     */
    public function deleteAction() {
// Create object for entity manager
        $entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');

        $id = (int) $this->params()->fromRoute('id', 0);

// Find bid detail
        $bid = $entityManager->find('CsnCms\Entity\Bid', $id);

        $bidName = $bid->getName();

// Delete query to delete this record
        $dql = "Delete from CsnCms\Entity\Bid a where a.bidid =" . $id;

        try {

// Get user identity
            $userData = $this->identity();

            $query = $entityManager->createQuery($dql);

// Extecute query
            $query->execute();

// Call activity model
            $activity = new ActivityModel();

            $type = 'Deleted';
// Save activity data
            $activity->saveActivity($entityManager, addslashes($bidName), $type, 'Bid', new \DateTime(date('Y-m-d h:i:s')), addslashes($userData->getUsername()));

            $this->flashMessenger()->addMessage('Bid Successfully Deleted');
        } catch (\Doctrine\DBAL\DBALException $e) {
            $this->flashMessenger()->addMessage('Cannot delete or update a parent row while other child values exist');
        }



        return $this->redirect()->toRoute('admin/default', array('controller' => 'bid', 'action' => 'index'));
    }

    /**
     * Get Form object method
     *
     * @param void
     * @return object
     */
    public function importAction() {
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headTitle('Import Company');


        $uri = $this->getRequest()->getBasePath();



        ini_set('max_execution_time', 600);
        $userData = $this->identity();
        $serviceLocator = $this->getServiceLocator();
        $adapter = $serviceLocator->get('Adapter');
        $session = new Container('base');
        $ImportBidId = $session->offsetGet('ImportBidId');
        $ImportFile = $session->offsetGet('ImportFile');

        $serviceLocator = $this->getServiceLocator();
        $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');
        $bid = $entityManager->find('CsnCms\Entity\Bid', $ImportBidId);
        $levyType = $bid->getLevytype();

        // Check for post data
        // Get request object
        $request = $this->getRequest();
        if ($request->isPost()) {
            $error = 0;
            if ($ImportFile != '') {
                $path = FILE_PATH . '/public/excel/' . $ImportFile;
                $file_handle = fopen($path, "r");
                $type = array();
                $sector = array();
                $category = array();
                $companies = array();
                $propertyStatus = array();
                $consult = array();
                $document = array();
                $projectDesc = array();
                $count = 0;
                setlocale(LC_ALL, 'fr_FR.UTF-8');
                $countNumber = 0;
                while (!feof($file_handle)) {
                    $line_of_text = fgetcsv($file_handle);
                    if (count($line_of_text) != 44 && $count == 0) {
                        $error = 1;
                        break;
                    }
                    
                    if ($count != 0 && ($line_of_text[0] == "" || $line_of_text[21] == "" || $line_of_text[27] == 0 || $line_of_text[27] == "" || $line_of_text[30] == 0 || $line_of_text[30] == "")) {
                        continue;
                    }
                    
                    if ($count != 0 && count($line_of_text) > 1) {

                        $companies[$count]['Name'] = $line_of_text[0];
                        $companies[$count]['BidID'] = $ImportBidId;
                        $companies[$count]['SectorID'] = $line_of_text[21];
                        IF ($line_of_text[20] != '')
                            $companies[$count]['TypeID'] = $line_of_text[20];
                        
                        IF ($line_of_text[22] != '')
                            $companies[$count]['CategoryID'] = $line_of_text[22];
                        $companies[$count]['UserID'] = $userData->getId();
                        $companies[$count]['UserCreated'] = $userData->getId();
                        $companies[$count]['UserModified'] = $userData->getId();
                        $companies[$count]['DateCreated'] = date('Y-m-d h:i:s');
                        $companies[$count]['DateModified'] = date('Y-m-d h:i:s');
                        for ($i = 0; $i < 6; $i++) {
                            if ($i == 0) {
                                $companies[$count]['Contact'][$i]['CompanyID'] = '';
                                $companies[$count]['Contact'][$i]['Title'] = $line_of_text[1];
                                $companies[$count]['Contact'][$i]['FirstName'] = $line_of_text[2];
                                $companies[$count]['Contact'][$i]['LastName'] = $line_of_text[3];
                                $companies[$count]['Contact'][$i]['Position'] = $line_of_text[4];
                                $companies[$count]['Contact'][$i]['Phone'] = $line_of_text[14];
                                $companies[$count]['Contact'][$i]['Mobile'] = $line_of_text[15];
                                $companies[$count]['Contact'][$i]['Fax'] = $line_of_text[16];
                                $companies[$count]['Contact'][$i]['Email'] = $line_of_text[17];
                                $companies[$count]['Contact'][$i]['Website'] = $line_of_text[18];
                                $companies[$count]['Contact'][$i]['FloorNo'] = $line_of_text[5];
                                $companies[$count]['Contact'][$i]['StreetNo'] = $line_of_text[6];
                                $companies[$count]['Contact'][$i]['Postcode'] = $line_of_text[12];
                                $companies[$count]['Contact'][$i]['Address1'] = $line_of_text[7];
                                $companies[$count]['Contact'][$i]['Address2'] = $line_of_text[8];
                                $companies[$count]['Contact'][$i]['Address3'] = $line_of_text[9];
                                $companies[$count]['Contact'][$i]['Address4'] = $line_of_text[10];
                                $companies[$count]['Contact'][$i]['Address5'] = $line_of_text[11];
                                $companies[$count]['Contact'][$i]['Type'] = $i + 1;
                            } else {
                                $companies[$count]['Contact'][$i]['CompanyID'] = '';
                                $companies[$count]['Contact'][$i]['Title'] = '';
                                $companies[$count]['Contact'][$i]['FirstName'] = '';
                                $companies[$count]['Contact'][$i]['LastName'] = '';
                                $companies[$count]['Contact'][$i]['Position'] = '';
                                $companies[$count]['Contact'][$i]['Phone'] = '';
                                $companies[$count]['Contact'][$i]['Mobile'] = '';
                                $companies[$count]['Contact'][$i]['Fax'] = '';
                                $companies[$count]['Contact'][$i]['Email'] = '';
                                $companies[$count]['Contact'][$i]['Website'] = '';
                                $companies[$count]['Contact'][$i]['FloorNo'] = '';
                                $companies[$count]['Contact'][$i]['StreetNo'] = '';
                                $companies[$count]['Contact'][$i]['Postcode'] = '';
                                $companies[$count]['Contact'][$i]['Address1'] = '';
                                $companies[$count]['Contact'][$i]['Address2'] = '';
                                $companies[$count]['Contact'][$i]['Address3'] = '';
                                $companies[$count]['Contact'][$i]['Address4'] = '';
                                $companies[$count]['Contact'][$i]['Address5'] = '';
                                $companies[$count]['Contact'][$i]['Type'] = $i + 1;
                            }
                        }

                        if ($line_of_text[24] != '' && $line_of_text[24] != 0)
                            $companies[$count]['Property'][0]['PropertyStatusID'] = $line_of_text[24];



                        $companies[$count]['Property'][0]['CompanyID'] = '';
                        if ($line_of_text[25] == 'Yes')
                            $companies[$count]['Property'][0]['BusinessRatepayer'] = 1;
                        else
                            $companies[$count]['Property'][0]['BusinessRatepayer'] = 0;

                        if ($line_of_text[26] == 'Yes')
                            $companies[$count]['Property'][0]['BidArea'] = 1;
                        else
                            $companies[$count]['Property'][0]['BidArea'] = 0;

                        $companies[$count]['Property'][0]['RV'] = $line_of_text[27];
                        $companies[$count]['Property'][0]['BandLevy'] = 0;
                        $companies[$count]['Property'][0]['LevyAmount'] = 0;

                        $totalValue = 0;

                        if ($levyType == 'Banded') {
                            // Get Rv Value based on bid
                            $rvValues = $entityManager->getRepository('CsnCms\Entity\RvValue')->findBy(array('bidid' => $ImportBidId));
                            foreach ($rvValues as $rvValueMatch) {
                                $from = $rvValueMatch->getFromvalue();
                                $value = $rvValueMatch->getTovalue();
                                $unlimit = $rvValueMatch->getUnlimited();
                                if ($line_of_text[27] >= $from && $line_of_text[27] <= $value) {
                                    $totalValue = $rvValueMatch->getValue();
                                } else if ($line_of_text[27] >= $from && $unlimit == 1) {
                                    $totalValue = $rvValueMatch->getValue();
                                }
                            }
                            $companies[$count]['Property'][0]['BandLevy'] = $totalValue;
                            $companies[$count]['Property'][0]['LevyAmount'] = $totalValue;
                        } else if ($levyType == 'Percentage Rate') {
                            // Get Rv % based on bid
                            $rvs = $entityManager->getRepository('CsnCms\Entity\Control')->findBy(array('bidid' => $ImportBidId, 'type' => 'RV'));
                            foreach ($rvs as $rv) {
                                $percenrageValue = $rv->getValue();
                                $totalValue = $percenrageValue * $line_of_text[27] / 100;
                                break;
                            }

                            $companies[$count]['Property'][0]['LevyAmount'] = $totalValue;
                        } else {
                            $companies[$count]['Property'][0]['LevyAmount'] = $totalValue;
                        }


                        $companies[$count]['Property'][0]['UprnNo'] = (is_numeric($line_of_text[30]) ? number_format($line_of_text[30], 0, '', '') : $line_of_text[30]);
                        $companies[$count]['Property'][0]['UserCreated'] = $userData->getId();
                        $companies[$count]['Property'][0]['UserModified'] = $userData->getId();
                        $companies[$count]['Property'][0]['DateCreated'] = date('Y-m-d h:i:s');
                        $companies[$count]['Property'][0]['DateModified'] = date('Y-m-d h:i:s');
                        $journalCount = 0;
                        $companies[$count]['Journal'] = array();
                        if ($line_of_text[31] == 'Voted') {
                            $companies[$count]['Journal'][0][$journalCount]['VotingHistory'] = 'Voted';
                            $companies[$count]['Journal'][0][$journalCount]['Type'] = 'Voting History';
                            $companies[$count]['Journal'][0][$journalCount]['DateCompleted'] = date('Y-m-d h:i:s');
                            $journalCount++;
                        } else if ($line_of_text[31] == 'Not Voted') {
                            $companies[$count]['Journal'][0][$journalCount]['VotingHistory'] = 'Not Voted';
                            $companies[$count]['Journal'][0][$journalCount]['Type'] = 'Voting History';
                            $companies[$count]['Journal'][0][$journalCount]['DateCompleted'] = date('Y-m-d h:i:s');
                            $journalCount++;
                        }

                        if ($line_of_text[32] == 'Yes') {
                            $companies[$count]['Journal'][0][$journalCount]['VotingInformation'] = 1;
                            $companies[$count]['Journal'][0][$journalCount]['Type'] = 'Voting Intention';
                            $companies[$count]['Journal'][0][$journalCount]['DateCompleted'] = date('Y-m-d h:i:s');
                            $journalCount++;
                        } else if ($line_of_text[32] == "Don't Know") {
                            $companies[$count]['Journal'][0][$journalCount]['VotingInformation'] = 2;
                            $companies[$count]['Journal'][0][$journalCount]['Type'] = 'Voting Intention';
                            $companies[$count]['Journal'][0][$journalCount]['DateCompleted'] = date('Y-m-d h:i:s');
                            $journalCount++;
                        } else if ($line_of_text[32] == 'No') {
                            $companies[$count]['Journal'][0][$journalCount]['VotingInformation'] = 0;
                            $companies[$count]['Journal'][0][$journalCount]['Type'] = 'Voting Intention';
                            $companies[$count]['Journal'][0][$journalCount]['DateCompleted'] = date('Y-m-d h:i:s');
                            $journalCount++;
                        }

                        if ($line_of_text[33] == 'Yes') {
                            $companies[$count]['Journal'][0][$journalCount]['TargetVoter'] = 1;
                            $companies[$count]['Journal'][0][$journalCount]['Type'] = 'Target Voter';
                            $companies[$count]['Journal'][0][$journalCount]['DateCompleted'] = date('Y-m-d h:i:s');
                            $journalCount++;
                        } else if ($line_of_text[33] == 'No') {
                            $companies[$count]['Journal'][0][$journalCount]['TargetVoter'] = 0;
                            $companies[$count]['Journal'][0][$journalCount]['Type'] = 'Target Voter';
                            $companies[$count]['Journal'][0][$journalCount]['DateCompleted'] = date('Y-m-d h:i:s');
                            $journalCount++;
                        }

                        if ($line_of_text[35] == 'Yes' || $line_of_text[35] == 'on') {
                            $companies[$count]['Journal'][0][$journalCount]['Type'] = 'Face to Face Meeting';
                            $companies[$count]['Journal'][0][$journalCount]['DateCompleted'] = date('Y-m-d h:i:s');
                            $journalCount++;
                        }

                        if ($line_of_text[36] == 'Yes' || $line_of_text[36] == 'on' || $line_of_text[36] != '') {
                            $companies[$count]['Journal'][0][$journalCount]['Type'] = 'Consultation Sheet';
                            $companies[$count]['Journal'][0][$journalCount]['ConsultationID'] = $line_of_text[34];
                            $consult[] = $line_of_text[34];
                            $companies[$count]['Journal'][0][$journalCount]['DateCompleted'] = date('Y-m-d h:i:s');
                            $journalCount++;
                        }

                        if ($line_of_text[37] == 'Yes' || $line_of_text[37] == 'on') {
                            $companies[$count]['Journal'][0][$journalCount]['Type'] = 'Sector/Street';
                            $companies[$count]['Journal'][0][$journalCount]['DateCompleted'] = date('Y-m-d h:i:s');
                            $journalCount++;
                        }

                        if ($line_of_text[38] == 'Yes' || $line_of_text[38] == 'on') {
                            $companies[$count]['Journal'][0][$journalCount]['Type'] = 'Telephone Survey';
                            $companies[$count]['Journal'][0][$journalCount]['DateCompleted'] = date('Y-m-d h:i:s');
                            $journalCount++;
                        }

                        if ($line_of_text[39] == 'Yes' || $line_of_text[39] == 'on') {
                            $companies[$count]['Journal'][0][$journalCount]['Type'] = 'Other';
                            $companies[$count]['Journal'][0][$journalCount]['DateCompleted'] = date('Y-m-d h:i:s');
                            $journalCount++;
                        }

                        if ($line_of_text[40] != '') {
                            $companies[$count]['Journal'][0][$journalCount]['Type'] = 'Document';
                            $companies[$count]['Journal'][0][$journalCount]['DocumentID'] = $line_of_text[40];
                            $companies[$count]['Journal'][0][$journalCount]['DateCompleted'] = date('Y-m-d h:i:s');
                            $journalCount++;
                            $document[] = $line_of_text[40];
                        }


                        $companies[$count]['Project'][0]['ProjectDescriptionID'] = $line_of_text[41];
                        $companies[$count]['Project'][0]['Value'] = $line_of_text[43];
                        $companies[$count]['Project'][0]['UserCreated'] = $userData->getId();
                        $companies[$count]['Project'][0]['UserModified'] = $userData->getId();
                        $companies[$count]['Project'][0]['Date'] = date('Y-m-d h:i:s');
                        $companies[$count]['Project'][0]['DateCreated'] = date('Y-m-d h:i:s');
                        $companies[$count]['Project'][0]['DateModified'] = date('Y-m-d h:i:s');

                        $projectDesc[] = $line_of_text[41];
                        $type[] = $line_of_text[20];
                        $sector[] = $line_of_text[21];
                        $category[] = $line_of_text[22];
                        $propertyStatus[] = $line_of_text[24];
                    }
                    $count++;
                }

                $type = array_unique($type);
                $sector = array_unique($sector);
                $category = array_unique($category);
                $propertyStatus = array_unique($propertyStatus);
                $document = array_unique($document);
                $consult = array_unique($consult);
                $projectDesc = array_unique($projectDesc);
                foreach ($type as $key => $value) {
                    if ($value != '') {
                        $vars = array('Name' => $value);
                        $this->insert_unique('mosaic_ctr_type', $vars, $adapter);
                    }
                }

                foreach ($sector as $key => $value) {
                    if ($value != '') {
                        $vars = array('Name' => $value, 'BidID' => $ImportBidId);
                        $this->insert_unique('mosaic_ctr_sector', $vars, $adapter);
                    }
                }
                foreach ($category as $key => $value) {
                    if ($value != '') {
                        $vars = array('Value' => $value, 'Type' => 'Category', 'BidID' => $ImportBidId);
                        $this->insert_unique('mosaic_ctr_control', $vars, $adapter);
                    }
                }
                foreach ($propertyStatus as $key => $value) {
                    if ($value != '') {
                        $vars = array('Name' => $value);
                        $this->insert_unique('mosaic_ctr_property', $vars, $adapter);
                    }
                }

                foreach ($document as $key => $value) {
                    if ($value != '') {
                        $vars = array('Name' => $value);
                        $this->insert_unique('mosaic_ctr_document_type', $vars, $adapter);
                    }
                }

                foreach ($consult as $key => $value) {
                    if ($value != '') {
                        $vars = array('Name' => $value);
                        $this->insert_unique('mosaic_ctr_consultation_preferences', $vars, $adapter);
                    }
                }

                foreach ($projectDesc as $key => $value) {
                    if ($value != '') {
                        $vars = array('Value' => $value, 'Type' => 'Project Description', 'BidID' => $ImportBidId);
                        $this->insert_unique('mosaic_ctr_control', $vars, $adapter);
                    }
                }
                foreach ($companies as $company) {
                    $contactArray = $company['Contact'];
                    $propertyArray = $company['Property'];
                    $JournalArray = $company['Journal'];
                    $ProjectArray = $company['Project'];
                    unset($company['Contact']);
                    unset($company['Property']);
                    unset($company['Journal']);
                    unset($company['Project']);
                    $SectorID = $this->fetch_value('mosaic_ctr_sector', "Name like '" . $company['SectorID'] . "'", $adapter);
                    $PropertyStatusID = array();
                    $ConsultationID = array();
                    if (isset($company['CategoryID'])) {
                        $CategoryID = $this->fetch_value('mosaic_ctr_control', "Value like '%" . $company['CategoryID'] . "%' and BidID=" . $ImportBidId . ' and Type ="Category"', $adapter);
                        foreach ($CategoryID as $category) {
                            $company['CategoryID'] = $category['ControlID'];
                        }
                    }
                    
                    if (isset($company['TypeID'])) {
                        $typeId = $this->fetch_value('mosaic_ctr_type', "Name like '%" . $company['TypeID'] . "%'", $adapter);
                        foreach ($typeId as $type) {
                            $company['TypeID'] = $type['TypeID'];
                        }
                    }
                    
                    
                    
                    foreach ($SectorID as $sector) {
                        $company['SectorID'] = $sector['SectorID'];
                    }
                    $companyId = $this->insert_value('mosaic_dat_company', $company, $adapter);
                    foreach ($contactArray as $contact) {
                        $contact['CompanyID'] = $companyId;
                        $contactId = $this->insert_value('mosaic_dat_company_contact', $contact, $adapter);
                    }
                    foreach ($propertyArray as $property) {

                        if (isset($property['PropertyStatusID']))
                            $PropertyStatusID = $this->fetch_value('mosaic_ctr_property', "Name like '%" . $property['PropertyStatusID'] . "%'", $adapter);

                        if (count($PropertyStatusID) > 0) {
                            foreach ($PropertyStatusID as $prop) {
                                $property['PropertyStatusID'] = $prop['PropertyID'];
                            }
                        }

                        $property['CompanyID'] = $companyId;
                        $propertyId = $this->insert_value('mosaic_dat_company_property', $property, $adapter);
                    }

                    foreach ($JournalArray as $jornals) {

                        foreach ($jornals as $jornal) {

                            $ConsultationID = array();
                            $DocumentID = array();
                            $jornal['UserCreated'] = $userData->getId();
                            $jornal['UserModified'] = $userData->getId();
                            $jornal['DateCreated'] = date('Y-m-d h:i:s');
                            $jornal['DateModified'] = date('Y-m-d h:i:s');

                            if (isset($jornal['ConsultationID']))
                                $ConsultationID = $this->fetch_value('mosaic_ctr_consultation_preferences', "Name like '%" . $jornal['ConsultationID'] . "%'", $adapter);

                            if (count($ConsultationID) > 0) {

                                foreach ($ConsultationID as $prop) {
                                    $jornal['ConsultationID'] = $prop['ConsultationID'];
                                }
                            }


                            if (isset($jornal['DocumentID']))
                                $DocumentID = $this->fetch_value('mosaic_ctr_document_type', "Name like '%" . $jornal['DocumentID'] . "%'", $adapter);

                            if (count($DocumentID) > 0) {

                                foreach ($DocumentID as $prop) {
                                    $jornal['DocumentID'] = $prop['DocumentID'];
                                }
                            }

                            $jornal['CompanyID'] = $companyId;

                            if (isset($jornal['Type']))
                                $jornalId = $this->insert_value('mosaic_dat_company_journal', $jornal, $adapter);
                        }
                    }

                    foreach ($ProjectArray as $project) {

                        if (isset($project['ProjectDescriptionID']) && $project['ProjectDescriptionID'] != '') {
                            $ProjectDescriptionID = $this->fetch_value('mosaic_ctr_control', "Value like '%" . $project['ProjectDescriptionID'] . "%' and BidID=" . $ImportBidId . ' and Type ="Project Description"', $adapter);


                            foreach ($ProjectDescriptionID as $category) {
                                $project['ProjectDescriptionID'] = $category['ControlID'];
                            }

                            $project['CompanyID'] = $companyId;
                            $projectId = $this->insert_value('mosaic_dat_company_project', $project, $adapter);
                        }
                    }
                }
                fclose($file_handle);
            }

            $this->flashMessenger()->addMessage('Successfully uploaded ' . ($count - 2) . ' Companies for the selected BID');
            return new ViewModel(array('success' => 1, 'message' => 'Successfully uploaded ' . ($count - 1) . ' Companies for the selected BID', 'ImportBidId' => $ImportBidId));
        } else {
            if ($ImportFile != '') {
                $path = FILE_PATH . '/public/excel/' . $ImportFile;
                $file_handle = fopen($path, "r");
                $count = 0;
                setlocale(LC_ALL, 'fr_FR.UTF-8');
                $countNumber = 0;
                $err = array();
                $error = 0;
                while (!feof($file_handle)) {
                    $line_of_text = fgetcsv($file_handle);
                    if (count($line_of_text) != 44 && $count == 0) {
                        $error = 2;
                        break;
                    }
                    if ($count != 0 && count($line_of_text) > 1) {
                        if ($line_of_text[0] == "" || $line_of_text[21] == "" || $line_of_text[27] == 0 || $line_of_text[27] == "" || $line_of_text[30] == 0 || $line_of_text[30] == "") {
                            $errorArr = array($line_of_text[0], $line_of_text[21], $line_of_text[27], $line_of_text[30]);
                            $err[] = $errorArr;
                            $error = 1;
                        }
                    }
                    $count++;
                }
                
                if ($error == 1) {
                    return new ViewModel(array('errors' => $err, 'message' => 'There were ' . count($err) . ' errors found.', 'error' => $error, 'ImportBidId' => $ImportBidId));
                }

                if ($error == 2) {
                    return new ViewModel(array('errors' => $err, 'message' => 'Template not matched with provided template.', 'error' => $error, 'ImportBidId' => $ImportBidId));
                }

                if ($error == 0) {
                    return new ViewModel(array('errors' => $err, 'message' => ($count - 2) . ' of records were found. To finish import select the "Finish Import" button', 'error' => $error, 'ImportBidId' => $ImportBidId));
                }
            }
        }
    }

    function insert_unique($table, $vars, $adapter) {
        if (count($vars)) {
//$table = mysql_real_escape_string($table);
            $vars = array_map('mysql_escape_string', $vars);
            $req = "INSERT INTO `$table` (`" . join('`, `', array_keys($vars)) . "`) ";
            $req .= "SELECT '" . join("', '", $vars) . "' FROM DUAL ";
            $req .= "WHERE NOT EXISTS (SELECT 1 FROM `$table` WHERE ";

            foreach ($vars AS $col => $val)
                $req .= "`$col`='$val' AND ";

            $req = substr($req, 0, -5) . ") LIMIT 1";
            $res = $adapter->query($req, array(5));
            return true;
        }
        return False;
    }

    function insert_value($table, $vars, $adapter) {
        if (count($vars) > 0) {
//$table = mysql_real_escape_string($table);
            $vars = array_map('mysql_escape_string', $vars);
            $req = "INSERT INTO `$table` (`" . join('`, `', array_keys($vars)) . "`) ";
            $req .= "SELECT '" . join("', '", $vars) . "' FROM DUAL ";

            $res = $adapter->query($req, array(5));

            return $adapter->getDriver()->getConnection()->getLastGeneratedValue();
        }
        return False;
    }

    function fetch_value($table, $where, $adapter) {

//$table = mysql_real_escape_string($table);
        $req = "select * from `$table` where " . $where;

        $res = $adapter->query($req, array(5));

        return $res;
    }

}
