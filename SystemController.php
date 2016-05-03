<?php

/**
 * coolcsn * Index Controller
 * @link https://github.com/coolcsn/CsnCms for the canonical source repository
 * @copyright Copyright (c) 2005-2013 LightSoft 2005 Ltd. Bulgaria
 * @license https://github.com/coolcsn/CsnCms/blob/master/LICENSE BSDLicense
 * @author Stoyan Cheresharov <stoyan@coolcsn.com>
 * @author Nikola Vasilev <niko7vasilev@gmail.com>
 * @author Svetoslav Chonkov <svetoslav.chonkov@gmail.com>
 * @author Stoyan Revov <st.revov@gmail.com>
 */

namespace CsnCms\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use CsnCms\Options\ModuleOptions;
use CsnCms\Entity\Type;
use CsnCms\Entity\Sector;
use CsnCms\Entity\Location;
use CsnCms\Entity\CompanyType;
use CsnCms\Entity\KeyProjectArea;
use CsnCms\Entity\RvValue;
use CsnCms\Entity\ConsultationPreferences;
use CsnCms\Entity\Payment;
use CsnCms\Entity\Property;
use CsnCms\Entity\FollowUpAction;
use CsnCms\Entity\SendTo;
use CsnCms\Entity\DocumentType;
use CsnCms\Form\SystemForm;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use CsnCms\Model\ActivityModel;
use Zend\Session\Container;
/**
 * <b>Authentication controller</b>
 * This controller has been build with educational purposes to demonstrate how authentication can be done
 */
class SystemController extends AbstractActionController {

    /**
     * @var ModuleOptions
     */
    protected $options;

    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * Index action
     *
     * The method show to users they are guests
     *
     * @return Zend\View\Model\ViewModelarray navigation menu
     */
    public function indexAction() {

        $user = $this->identity();
        $entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $type = $entityManager->getRepository('CsnCms\Entity\Type')->findAll();
        $sector = $entityManager->getRepository('CsnCms\Entity\Sector')->findAll();
        $consultationPreferences = $entityManager->getRepository('CsnCms\Entity\ConsultationPreferences')->findAll();
        $payment = $entityManager->getRepository('CsnCms\Entity\Payment')->findAll();
        $property = $entityManager->getRepository('CsnCms\Entity\Property')->findAll();
        $documentType = $entityManager->getRepository('CsnCms\Entity\DocumentType')->findAll();
        $sendTo = $entityManager->getRepository('CsnCms\Entity\SendTo')->findAll();
        $followUpAction = $entityManager->getRepository('CsnCms\Entity\FollowUpAction')->findAll();
        $location = $entityManager->getRepository('CsnCms\Entity\Location')->findAll();
        $companyType = $entityManager->getRepository('CsnCms\Entity\CompanyType')->findAll();
        $keyProjectArea = $entityManager->getRepository('CsnCms\Entity\KeyProjectArea')->findAll();
        return new ViewModel(array('typeList' => $type, 'sectorList' => $sector, 'consultationPreferencesList' => $consultationPreferences, 'paymentList' => $payment, 'propertyList' => $property, 'documentTypeList' => $documentType, 'sendToList' => $sendTo, 'followUpActionList' => $followUpAction,'locationList'=>$location,'companyTypeList'=>$companyType,'keyProjectAreas'=>$keyProjectArea));
    }

    /**
     * Get Form object method
     *
     * @param void
     * @return object
     */
    protected function getForm() {

        // Create entity manager object
        $entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $form = new SystemForm($entityManager);
        return $form;
    }

    /**
     * Add action for bid page
     *
     * @param void
     * @return object
     */
    public function addAction() {

        // Create entity manager object
        $entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $type = $this->getRequest()->getQuery('type');
        $entity = $this->getRequest()->getQuery('entity');
        // Set Layout
        $this->layout('layout/ajax');
        $viewmodel = new ViewModel();
        $request = $this->getRequest();
        $is_xmlhttprequest = 1;
        $entityId = "";
        $id = (int) $this->params()->fromRoute('id', 0);
        $mode = '';
        $control = '';
        //disable layout if request by Ajax
        //$viewmodel->setTerminal($request->isXmlHttpRequest());
        switch ($entity) {
            case 'Type':
                $entityId = 'typeid';
                $control = $entityManager->find("CsnCms\Entity\\" . $entity, $id);
                if (!$control instanceof Type) {
                    $control = new Type();
                    $mode = "Add " . $type;
                }else{
                    $mode = "Edit " . $type;
                }
                break;
            case 'Sector':
                $entityId = 'sectorid';
                $control = $entityManager->find("CsnCms\Entity\\" . $entity, $id);
                if (!$control instanceof Sector) {
                    $control = new Sector();
                    $mode = "Add " . $type;
                }else{
                    $mode = "Edit " . $type;
                }
                break;
            case 'ConsultationPreferences':
                $entityId = 'consultationid';
                $control = $entityManager->find("CsnCms\Entity\\" . $entity, $id);
                if (!$control instanceof ConsultationPreferences) {
                    $control = new ConsultationPreferences();
                    $mode = "Add Consultation Preferences";
                }else{
                    $mode = "Edit Consultation Preferences";
                }
                break;
            case 'Payment':
                $entityId = 'paymentid';
                $control = $entityManager->find("CsnCms\Entity\\" . $entity, $id);
                if (!$control instanceof Payment) {
                    $control = new Payment();
                    $mode = "Add " . $type;
                }else{
                    $mode = "Edit ".$type;
                }
                break;
            case 'Property':
                $entityId = 'propertyid';
                $control = $entityManager->find("CsnCms\Entity\\" . $entity, $id);
                if (!$control instanceof Property) {
                    $control = new Property();
                    $mode = "Add " . $type;
                }else{
                    $mode = "Edit ".$type;
                }
                break;
            case 'DocumentType':
                $entityId = 'documentid';
                $control = $entityManager->find("CsnCms\Entity\\" . $entity, $id);
                if (!$control instanceof DocumentType) {
                    $control = new DocumentType();
                    $mode = "Add Document Type";
                }else{
                    $mode = "Edit Document Type";
                }
                break;
            case 'FollowUpAction':
                $entityId = 'followupid';
                $control = $entityManager->find("CsnCms\Entity\\" . $entity, $id);
                if (!$control instanceof FollowUpAction) {
                    $control = new FollowUpAction();
                    $mode = "Add Follow Up Action";
                }else{
                    $mode = "Edit Follow Up Action";
                }
                break;
            case 'SendTo':
                $entityId = 'sendid';
                $control = $entityManager->find("CsnCms\Entity\\" . $entity, $id);
                if (!$control instanceof SendTo) {
                    $control = new SendTo();
                    $mode = "Add Send To";
                }else{
                    $mode = "Edit Send To";
                }
                break;
            case 'Location':
                $entityId = 'location';
                $control = $entityManager->find("CsnCms\Entity\\" . $entity, $id);
                if (!$control instanceof Location) {
                    $control = new Location();
                    $mode = "Add " . $type;
                }else{
                    $mode = "Edit " . $type;
                }
                break;
            case 'CompanyType':
                $entityId = 'CompanyType';
                $control = $entityManager->find("CsnCms\Entity\\" . $entity, $id);
                if (!$control instanceof CompanyType) {
                    $control = new CompanyType();
                    $mode = "Add Company Type";
                }else{
                    $mode = "Edit Company Type";
                }
                break;
                
            case 'KeyProjectArea':
                $entityId = 'KeyProjectArea';
                $control = $entityManager->find("CsnCms\Entity\\" . $entity, $id);
                if (!$control instanceof KeyProjectArea) {
                    $control = new KeyProjectArea();
                    $mode = "Add Key Project Area";
                }else{
                    $mode = "Edit Key Project Area";
                }
                break;
        }

        // Create an object for form        
        $form = $this->getForm();

        // assign hydrator
        $hydrator = new DoctrineHydrator($entityManager, get_class($control));
        $form->setHydrator($hydrator);
        // bind object to form
        $form->bind($control);
        $form->get('type')->setValue($type);
        $form->get('entityid')->setValue($id);
        $form->get('entity')->setValue($entity);

        if (!$request->isXmlHttpRequest()) {
            //if NOT using Ajax
            $is_xmlhttprequest = 0;
            if ($request->isPost()) {
                $form->setData($request->getPost());
                if ($form->isValid()) {
                    // TODO
                }
            }
        }
        $viewmodel->setVariables(array(
            'form' => $form,
            // is_xmlhttprequest is needed for check this form is in modal dialog or not
            // in view
            'is_xmlhttprequest' => $is_xmlhttprequest,
            'mode' => $mode,
            'type' => $type
        ));

        return $viewmodel;
    }

    /**
     * Validate post action for shipping or billing
     *
     * @param void
     * @return object
     */
    public function validatePostAjaxAction() {

        $session = new Container('base');
        $bidSession = $session->offsetGet('BidId');
        $bidId = '';
        $user = $this->identity();
        if (isset($bidSession))
            $bidId = $session->offsetGet('BidId');
        else
            $bidId = $user->getBidId();
        
        // Access request and response
        $request = $this->getRequest();
        $response = $this->getResponse();
        $type = $request->getPost('type');
        $entity = $request->getPost('entity');
        $id = $request->getPost('entityid');
        $control = "";
        $form = "";
        // Create object for entity manager
        $entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');

        switch ($entity) {
            case 'Type':
                $entityId = 'typeid';
                $control = $entityManager->find("CsnCms\Entity\\" . $entity, $id);
                if (!$control instanceof Type) {
                    $control = new Type();
                    $mode = "Add " . $type;
                }
                break;
            case 'Sector':
                $entityId = 'sectorid';
                $control = $entityManager->find("CsnCms\Entity\\" . $entity, $id);
                if (!$control instanceof Sector) {
                    $control = new Sector();
                    $mode = "Add " . $type;
                }
                break;
            case 'ConsultationPreferences':
                $entityId = 'consultationid';
                $control = $entityManager->find("CsnCms\Entity\\" . $entity, $id);
                if (!$control instanceof ConsultationPreferences) {
                    $control = new ConsultationPreferences();
                    $mode = "Add Consultation Preferences";
                }
                break;
            case 'Payment':
                $entityId = 'paymentid';
                $control = $entityManager->find("CsnCms\Entity\\" . $entity, $id);
                if (!$control instanceof Payment) {
                    $control = new Payment();
                    $mode = "Add " . $type;
                }
                break;
            case 'Property':
                $entityId = 'propertyid';
                $control = $entityManager->find("CsnCms\Entity\\" . $entity, $id);
                if (!$control instanceof Property) {
                    $control = new Property();
                    $mode = "Add " . $type;
                }
                break;
            case 'DocumentType':
                $entityId = 'documentid';
                $control = $entityManager->find("CsnCms\Entity\\" . $entity, $id);
                if (!$control instanceof DocumentType) {
                    $control = new DocumentType();
                    $mode = "Add Document Type";
                }
                break;
            case 'FollowUpAction':
                $entityId = 'followupid';
                $control = $entityManager->find("CsnCms\Entity\\" . $entity, $id);
                if (!$control instanceof FollowUpAction) {
                    $control = new FollowUpAction();
                    $mode = "Add Follow Up Action";
                }
                break;
            case 'SendTo':
                $entityId = 'sendid';
                $control = $entityManager->find("CsnCms\Entity\\" . $entity, $id);
                if (!$control instanceof SendTo) {
                    $control = new SendTo();
                    $mode = "Add " . $type;
                }
                break;
            case 'Location':
                $entityId = 'location';
                $control = $entityManager->find("CsnCms\Entity\\" . $entity, $id);
                if (!$control instanceof Location) {
                    $control = new Location();
                    $mode = "Add " . $type;
                }
                break;
             case 'CompanyType':
                $entityId = 'CompanyType';
                $control = $entityManager->find("CsnCms\Entity\\" . $entity, $id);
                if (!$control instanceof CompanyType) {
                    $control = new CompanyType();
                    $mode = "Add Company Type";
                }
                break;
             case 'KeyProjectArea':
                $entityId = 'KeyProjectArea';
                $control = $entityManager->find("CsnCms\Entity\\" . $entity, $id);
                if (!$control instanceof KeyProjectArea) {
                    $control = new KeyProjectArea();
                    $mode = "Add Key Project Area";
                }
                break;
        }

        $form = $this->getForm();
        $hydrator = new DoctrineHydrator($entityManager, get_class($control));
        $form->setHydrator($hydrator);
        // bind object to form
        $form->bind($control);
        $messages = array();
        // Check for post data
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);
            // Validate form data
            if (!$form->isValid()) {
                $errors = $form->getMessages();
                foreach ($errors as $key => $row) {
                    if (!empty($row) && $key != 'submit') {
                        foreach ($row as $keyer => $rower) {
                            $messages[$key][] = $rower;
                        }
                    }
                }
            }

            // Set Message for error
            if (!empty($messages)) {
                $response->setContent(\Zend\Json\Json::encode($messages));
            } else {
                //userData = $this->identity();
                // Call activity model
                $activity = new ActivityModel();
                $type = ($id == 0 ? 'Added' : 'Updated');
                // Persist data to database
                $control->setName($request->getPost('name'));
                if($entity=='Sector'){
                    $control->setBidid($bidId);
                }
                $entityManager->persist($control);
                // Save activity data
                //$activity->saveActivity($entityManager, addslashes($control->getValue()), $type, addslashes($control->getType()), new \DateTime(date('Y-m-d h:i:s')), addslashes($userData->getUsername()));
                $entityManager->flush();
                $response->setContent(\Zend\Json\Json::encode(array('success' => 1)));
            }
        }
        return $response;
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
        $entity = $this->getRequest()->getQuery('entity');
        $control = "";
        // Find bid detail
        switch ($entity) {
            case 'Type':
                $control = $entityManager->find("CsnCms\Entity\\" . $entity, $id);
                break;
            case 'Sector':
                $control = $entityManager->find("CsnCms\Entity\\" . $entity, $id);
                break;
            case 'ConsultationPreferences':
                $control = $entityManager->find("CsnCms\Entity\\" . $entity, $id);
                break;
            case 'Payment':
                $control = $entityManager->find("CsnCms\Entity\\" . $entity, $id);
                break;
            case 'Property':
                $control = $entityManager->find("CsnCms\Entity\\" . $entity, $id);
                break;
            case 'DocumentType':
                $control = $entityManager->find("CsnCms\Entity\\" . $entity, $id);
                break;
            case 'FollowUpAction':
                $control = $entityManager->find("CsnCms\Entity\\" . $entity, $id);
                break;
            case 'SendTo':
                $control = $entityManager->find("CsnCms\Entity\\" . $entity, $id);
                break;
            case 'CompanyType':
                $control = $entityManager->find("CsnCms\Entity\\" . $entity, $id);
                break;
            case 'KeyProjectArea':
                $control = $entityManager->find("CsnCms\Entity\\" . $entity, $id);
                break;
            case 'Location':
                $control = $entityManager->find("CsnCms\Entity\\" . $entity, $id);
                break;
        }
        
        try {
            $entityManager->remove($control);
            $entityManager->flush();
            $this->flashMessenger()->addMessage('Successfully Deleted');
        } catch (\Doctrine\DBAL\DBALException $e) {
            $this->flashMessenger()->addMessage('Cannot delete or update a parent row while other child values exist');
        }
        
        
        return $this->redirect()->toRoute('admin/default', array('controller' => 'system', 'action' => 'index'));
    }

}
