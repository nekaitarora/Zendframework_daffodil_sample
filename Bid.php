<?php

namespace CsnCms\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Bid
 *
 * @ORM\Table(name="mosaic_dat_bid", indexes={@ORM\Index(name="LocationID", columns={"LocationID"}), @ORM\Index(name="CompanyTypeID", columns={"CompanyTypeID"})})
 * @ORM\Entity
 */
class Bid {

    /**
     * @var integer
     *
     * @ORM\Column(name="BidID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $bidid;

    /**
     * @var string
     *
     * @ORM\Column(name="Name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="BidProposer", type="string", length=255, nullable=false)
     */
    private $bidproposer;

    /**
     * @var integer
     *
     * @ORM\Column(name="BusinessesNumber", type="integer", nullable=false)
     */
    private $businessesnumber;

    /**
     * @var integer
     *
     * @ORM\Column(name="RVSize", type="integer", nullable=false)
     */
    private $rvsize;

    /**
     * @var integer
     *
     * @ORM\Column(name="StreetsNumber", type="integer", nullable=false)
     */
    private $streetsnumber;

    /**
     * @var integer
     *
     * @ORM\Column(name="SectorNumber", type="integer", nullable=false)
     */
    private $sectornumber;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="RenewalDate", type="date", nullable=false)
     */
    private $renewaldate;

    /**
     * @var string
     *
     * @ORM\Column(name="BidMap", type="string", length=255, nullable=false)
     */
    private $bidmap;

    /**
     * @var string
     *
     * @ORM\Column(name="BidData", type="string", length=255, nullable=false)
     */
    private $biddata;

    /**
     * @var integer
     *
     * @ORM\Column(name="Results", type="integer", nullable=true)
     */
    private $results;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="StartDate", type="date", nullable=true)
     */
    private $startdate;

    /**
     * @var string
     *
     * @ORM\Column(name="Term", type="string", length=255, nullable=true)
     */
    private $term;

    /**
     * @var string
     *
     * @ORM\Column(name="RenewalNumber", type="string", length=255, nullable=true)
     */
    private $renewalnumber;

    /**
     * @var string
     *
     * @ORM\Column(name="LevyType", type="string", length=255, nullable=true)
     */
    private $levytype;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="PercentageLevy", type="integer", nullable=false)
     */
    private $percentagelevy;

    /**
     * @var integer
     *
     * @ORM\Column(name="LavyRaisedAnnually", type="integer", nullable=true)
     */
    private $lavyraisedannually;

    /**
     * @var integer
     *
     * @ORM\Column(name="AditionalIncomeAnnually", type="integer", nullable=true)
     */
    private $aditionalincomeannually;

    /**
     * @var integer
     *
     * @ORM\Column(name="TotalBudget", type="integer", nullable=true)
     */
    private $totalbudget;

    /**
     * @var string
     *
     * @ORM\Column(name="ProjectArea", type="string", length=255, nullable=true)
     */
    private $projectarea;

    /**
     * @var integer
     *
     * @ORM\Column(name="StaffCount", type="integer", nullable=true)
     */
    private $staffcount;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="SizeOfBoard", type="integer", nullable=true)
     */
    private $sizeofboard;

    /**
     * @var integer
     *
     * @ORM\Column(name="UserCreated", type="integer", nullable=false)
     */
    private $usercreated;

    /**
     * @var integer
     *
     * @ORM\Column(name="UserModified", type="integer", nullable=false)
     */
    private $usermodified;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DateCreated", type="datetime", nullable=false)
     */
    private $datecreated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DateModified", type="datetime", nullable=true)
     */
    private $datemodified;

    /**
     * @var integer
     *
     * @ORM\Column(name="IpAddress", type="integer", nullable=false)
     */
    private $ipaddress;

    /**
     * @var \CsnCms\Entity\Location
     *
     * @ORM\ManyToOne(targetEntity="CsnCms\Entity\Location")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="LocationID", referencedColumnName="LocationID")
     * })
     */
    private $locationid;

    /**
     * @var \CsnCms\Entity\CompanyType
     *
     * @ORM\ManyToOne(targetEntity="CsnCms\Entity\CompanyType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="CompanyTypeID", referencedColumnName="CompanyTypeID")
     * })
     */
    private $companytypeid;

    /**
     * Get bidid
     *
     * @return integer 
     */
    public function getBidid() {
        return $this->bidid;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Bid
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set bidproposer
     *
     * @param string $bidproposer
     *
     * @return Bid
     */
    public function setBidproposer($bidproposer) {
        $this->bidproposer = $bidproposer;

        return $this;
    }

    /**
     * Get bidproposer
     *
     * @return string 
     */
    public function getBidproposer() {
        return $this->bidproposer;
    }

    /**
     * Set businessesnumber
     *
     * @param integer $businessesnumber
     *
     * @return Bid
     */
    public function setBusinessesnumber($businessesnumber) {
        $this->businessesnumber = $businessesnumber;

        return $this;
    }

    /**
     * Get businessesnumber
     *
     * @return integer 
     */
    public function getBusinessesnumber() {
        return $this->businessesnumber;
    }

    /**
     * Set rvsize
     *
     * @param integer $rvsize
     *
     * @return Bid
     */
    public function setRvsize($rvsize) {
        $this->rvsize = $rvsize;

        return $this;
    }

    /**
     * Get rvsize
     *
     * @return integer 
     */
    public function getRvsize() {
        return $this->rvsize;
    }

    /**
     * Set streetsnumber
     *
     * @param integer $streetsnumber
     *
     * @return Bid
     */
    public function setStreetsnumber($streetsnumber) {
        $this->streetsnumber = $streetsnumber;

        return $this;
    }

    /**
     * Get streetsnumber
     *
     * @return integer 
     */
    public function getStreetsnumber() {
        return $this->streetsnumber;
    }

    /**
     * Set sectornumber
     *
     * @param integer $sectornumber
     *
     * @return Bid
     */
    public function setSectornumber($sectornumber) {
        $this->sectornumber = $sectornumber;

        return $this;
    }

    /**
     * Get sectornumber
     *
     * @return integer 
     */
    public function getSectornumber() {
        return $this->sectornumber;
    }

    /**
     * Set renewaldate
     *
     * @param \DateTime $renewaldate
     *
     * @return Bid
     */
    public function setRenewaldate($renewaldate) {
        $this->renewaldate = $renewaldate;

        return $this;
    }

    /**
     * Get renewaldate
     *
     * @return \DateTime 
     */
    public function getRenewaldate() {
        return $this->renewaldate;
    }

    /**
     * Set bidmap
     *
     * @param string $bidmap
     *
     * @return Bid
     */
    public function setBidmap($bidmap) {
        $this->bidmap = $bidmap;

        return $this;
    }

    /**
     * Get bidmap
     *
     * @return string 
     */
    public function getBidmap() {
        return $this->bidmap;
    }

    /**
     * Set biddata
     *
     * @param string $biddata
     *
     * @return Bid
     */
    public function setBiddata($biddata) {
        $this->biddata = $biddata;

        return $this;
    }

    /**
     * Get biddata
     *
     * @return string 
     */
    public function getBiddata() {
        return $this->biddata;
    }

    /**
     * Set results
     *
     * @param integer $results
     *
     * @return Bid
     */
    public function setResults($results) {
        $this->results = $results;

        return $this;
    }

    /**
     * Get results
     *
     * @return integer 
     */
    public function getResults() {
        return $this->results;
    }

    /**
     * Set startdate
     *
     * @param \DateTime $startdate
     *
     * @return Bid
     */
    public function setStartdate($startdate) {
        $this->startdate = $startdate;

        return $this;
    }

    /**
     * Get startdate
     *
     * @return \DateTime 
     */
    public function getStartdate() {
        return $this->startdate;
    }

    /**
     * Set term
     *
     * @param string $term
     *
     * @return Bid
     */
    public function setTerm($term) {
        $this->term = $term;

        return $this;
    }

    /**
     * Get term
     *
     * @return string 
     */
    public function getTerm() {
        return $this->term;
    }

    /**
     * Set renewalnumber
     *
     * @param string $renewalnumber
     *
     * @return Bid
     */
    public function setRenewalnumber($renewalnumber) {
        $this->renewalnumber = $renewalnumber;

        return $this;
    }

    /**
     * Get renewalnumber
     *
     * @return string 
     */
    public function getRenewalnumber() {
        return $this->renewalnumber;
    }

    /**
     * Set levytype
     *
     * @param string $levytype
     *
     * @return Bid
     */
    public function setLevytype($levytype) {
        $this->levytype = $levytype;

        return $this;
    }

    /**
     * Get levytype
     *
     * @return string 
     */
    public function getLevytype() {
        return $this->levytype;
    }

    /**
     * Set lavyraisedannually
     *
     * @param integer $lavyraisedannually
     *
     * @return Bid
     */
    public function setLavyraisedannually($lavyraisedannually) {
        $this->lavyraisedannually = $lavyraisedannually;

        return $this;
    }

    /**
     * Get lavyraisedannually
     *
     * @return integer 
     */
    public function getLavyraisedannually() {
        return $this->lavyraisedannually;
    }

    /**
     * Set aditionalincomeannually
     *
     * @param integer $aditionalincomeannually
     *
     * @return Bid
     */
    public function setAditionalincomeannually($aditionalincomeannually) {
        $this->aditionalincomeannually = $aditionalincomeannually;

        return $this;
    }

    /**
     * Get aditionalincomeannually
     *
     * @return integer 
     */
    public function getAditionalincomeannually() {
        return $this->aditionalincomeannually;
    }

    /**
     * Set totalbudget
     *
     * @param integer $totalbudget
     *
     * @return Bid
     */
    public function setTotalbudget($totalbudget) {
        $this->totalbudget = $totalbudget;

        return $this;
    }

    /**
     * Get totalbudget
     *
     * @return integer 
     */
    public function getTotalbudget() {
        return $this->totalbudget;
    }
    
    /**
     * Set percentagelevy
     *
     * @param integer $percentagelevy
     *
     * @return Bid
     */
    public function setPercentagelevy($percentagelevy) {
        $this->percentagelevy = $percentagelevy;

        return $this;
    }

    /**
     * Get percentagelevy
     *
     * @return integer 
     */
    public function getPercentagelevy() {
        return $this->percentagelevy;
    }

    /**
     * Set projectarea
     *
     * @param string $projectarea
     *
     * @return Bid
     */
    public function setProjectarea($projectarea) {
        $this->projectarea = $projectarea;

        return $this;
    }

    /**
     * Get projectarea
     *
     * @return string 
     */
    public function getProjectarea() {
        return $this->projectarea;
    }

    /**
     * Set staffcount
     *
     * @param integer $staffcount
     *
     * @return Bid
     */
    public function setStaffcount($staffcount) {
        $this->staffcount = $staffcount;

        return $this;
    }

    /**
     * Get staffcount
     *
     * @return integer 
     */
    public function getStaffcount() {
        return $this->staffcount;
    }
    
    /**
     * Set sizeofboard
     *
     * @param integer SizeOfBoard
     *
     * @return Bid
     */
    public function setSizeofboard($sizeofboard) {
        $this->sizeofboard = $sizeofboard;

        return $this;
    }

    /**
     * Get sizeofboard
     *
     * @return integer 
     */
    public function getSizeofboard() {
        return $this->sizeofboard;
    }

    /**
     * Set usercreated
     *
     * @param integer $usercreated
     *
     * @return Bid
     */
    public function setUsercreated($usercreated) {
        $this->usercreated = $usercreated;

        return $this;
    }

    /**
     * Get usercreated
     *
     * @return integer 
     */
    public function getUsercreated() {
        return $this->usercreated;
    }

    /**
     * Set usermodified
     *
     * @param integer $usermodified
     *
     * @return Bid
     */
    public function setUsermodified($usermodified) {
        $this->usermodified = $usermodified;

        return $this;
    }

    /**
     * Get usermodified
     *
     * @return integer 
     */
    public function getUsermodified() {
        return $this->usermodified;
    }

    /**
     * Set datecreated
     *
     * @param \DateTime $datecreated
     *
     * @return Bid
     */
    public function setDatecreated($datecreated) {
        $this->datecreated = $datecreated;

        return $this;
    }

    /**
     * Get datecreated
     *
     * @return \DateTime 
     */
    public function getDatecreated() {
        return $this->datecreated;
    }

    /**
     * Set datemodified
     *
     * @param \DateTime $datemodified
     *
     * @return Bid
     */
    public function setDatemodified($datemodified) {
        $this->datemodified = $datemodified;

        return $this;
    }

    /**
     * Get datemodified
     *
     * @return \DateTime 
     */
    public function getDatemodified() {
        return $this->datemodified;
    }

    /**
     * Set ipaddress
     *
     * @param integer $ipaddress
     *
     * @return Bid
     */
    public function setIpaddress($ipaddress) {
        $this->ipaddress = $ipaddress;

        return $this;
    }

    /**
     * Get ipaddress
     *
     * @return integer 
     */
    public function getIpaddress() {
        return $this->ipaddress;
    }

    /**
     * Set locationid
     *
     * @param \CsnCms\Entity\Location $locationid
     *
     * @return Bid
     */
    public function setLocationid(\CsnCms\Entity\Location $locationid = null) {
        $this->locationid = $locationid;

        return $this;
    }

    /**
     * Get locationid
     *
     * @return \CsnCms\Entity\Location 
     */
    public function getLocationid() {
        return $this->locationid;
    }

    /**
     * Set companytypeid
     *
     * @param \CsnCms\Entity\CompanyType $companytypeid
     *
     * @return Bid
     */
    public function setCompanytypeid(\CsnCms\Entity\CompanyType $companytypeid = null) {
        $this->companytypeid = $companytypeid;

        return $this;
    }

    /**
     * Get companytypeid
     *
     * @return \CsnCms\Entity\CompanyType 
     */
    public function getCompanytypeid() {
        return $this->companytypeid;
    }

}
