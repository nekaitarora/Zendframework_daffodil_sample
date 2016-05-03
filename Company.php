<?php

namespace CsnCms\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Company
 *
 * @ORM\Table(name="mosaic_dat_company", indexes={@ORM\Index(name="UserID", columns={"UserID"}), @ORM\Index(name="MapReferenceID", columns={"MapReferenceID"}), @ORM\Index(name="SectorID", columns={"SectorID"}), @ORM\Index(name="CategoryID", columns={"CategoryID"}), @ORM\Index(name="TypeID", columns={"TypeID"})})
 * @ORM\Entity
 */
class Company {

    /**
     * @var integer
     *
     * @ORM\Column(name="CompanyID", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $companyid;

    /**
     * @var string
     *
     * @ORM\Column(name="Name", type="string", length=255, precision=0, scale=0, nullable=false, unique=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="CompanyType", type="string", length=255, precision=0, scale=0, unique=false)
     */
    private $companytype;

    /**
     * @var string
     *
     * @ORM\Column(name="AddressBit", type="string", length=255, precision=0, scale=0, nullable=false, unique=false)
     */
    private $addressbit;

    /**
     * @var string
     *
     * @ORM\Column(name="Description", type="text", precision=0, scale=0, nullable=false, unique=false)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="UserCreated", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $usercreated;

    /**
     * @var integer
     *
     * @ORM\Column(name="UserModified", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $usermodified;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DateCreated", type="datetime", precision=0, scale=0, nullable=false, unique=false)
     */
    private $datecreated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DateModified", type="datetime", precision=0, scale=0, nullable=false, unique=false)
     */
    private $datemodified;

    /**
     * @var integer
     *
     * @ORM\Column(name="IpAddress", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $ipaddress;

    /**
     * @var \CsnCms\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="CsnUser\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="UserID", referencedColumnName="UserID", nullable=true)
     * })
     */
    private $userid;

    /**
     * @var \CsnCms\Entity\Bid
     *
     * @ORM\ManyToOne(targetEntity="CsnCms\Entity\Bid")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="BidID", referencedColumnName="BidID", nullable=true)
     * })
     */
    private $bidid;

    /**
     * @var string
     *
     * @ORM\Column(name="MapReferenceID", type="string", length=255, precision=0, scale=0, nullable=false, unique=false)
     */
    private $mapreferenceid;

    /**
     * @var \CsnCms\Entity\Sector
     *
     * @ORM\ManyToOne(targetEntity="CsnCms\Entity\Sector")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="SectorID", referencedColumnName="SectorID", nullable=true)
     * })
     */
    private $sectorid;

    /**
     * @var \CsnCms\Entity\Control
     *
     * @ORM\ManyToOne(targetEntity="CsnCms\Entity\Control")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="CategoryID", referencedColumnName="ControlID", nullable=true)
     * })
     */
    private $categoryid;

    /**
     * @var \CsnCms\\Entity\Type
     *
     * @ORM\ManyToOne(targetEntity="CsnCms\Entity\Type")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="TypeID", referencedColumnName="TypeID", nullable=true)
     * })
     */
    private $typeid;

    /**
     * Get companyid
     *
     * @return integer 
     */
    public function getCompanyid() {
        return $this->companyid;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Company
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
     * Set companytype
     *
     * @param string $companytype
     *
     * @return Company
     */
    public function setCompanytype($companytype) {
        $this->companytype = $companytype;

        return $this;
    }

    /**
     * Get companytype
     *
     * @return string 
     */
    public function getCompanytype() {
        return $this->companytype;
    }

    /**
     * Set addressbit
     *
     * @param string $addressbit
     *
     * @return Company
     */
    public function setAddressbit($addressbit) {
        $this->addressbit = $addressbit;

        return $this;
    }

    /**
     * Get addressbit
     *
     * @return string 
     */
    public function getAddressbit() {
        return $this->addressbit;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Company
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Set usercreated
     *
     * @param integer $usercreated
     *
     * @return Company
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
     * @return Company
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
     * @return Company
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
     * @return Company
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
     * @return Company
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
     * Set userid
     *
     * @param \CsnUser\Entity\User $userid
     *
     * @return Company
     */
    public function setUserid(\CsnUser\Entity\User $userid = null) {
        $this->userid = $userid;

        return $this;
    }

    /**
     * Get userid
     *
     * @return \CsnUser\Entity\User 
     */
    public function getUserid() {
        return $this->userid;
    }

    /**
     * Set userid
     *
     * @param \CsnCms\Entity\User $userid
     *
     * @return Company
     */
    public function setBidid(\CsnCms\Entity\Bid $bidid = null) {
        $this->bidid = $bidid;

        return $this;
    }

    /**
     * Get userid
     *
     * @return \CsnCms\Entity\User 
     */
    public function getBidid() {
        return $this->bidid;
    }

    /**
     * Set mapreferenceid
     *
     * @return string 
     */
    public function setMapreferenceid($mapreferenceid) {
        $this->mapreferenceid = $mapreferenceid;

        return $this;
    }

    /**
     * Get mapreferenceid
     *
     * @return string 
     */
    public function getMapreferenceid() {
        return $this->mapreferenceid;
    }

    /**
     * Set sectorid
     *
     * @param \CsnCms\Entity\Sector $sectorid
     *
     * @return Company
     */
    public function setSectorid(\CsnCms\Entity\Sector $sectorid = null) {
        $this->sectorid = $sectorid;

        return $this;
    }

    /**
     * Get sectorid
     *
     * @return \CsnCms\\Entity\Sector 
     */
    public function getSectorid() {
        return $this->sectorid;
    }

    /**
     * Set categoryid
     *
     * @param \CsnCms\Entity\Control $categoryid
     *
     * @return Company
     */
    public function setCategoryid(\CsnCms\Entity\Control $categoryid = null) {
        $this->categoryid = $categoryid;

        return $this;
    }

    /**
     * Get categoryid
     *
     * @return \CsnCms\Entity\Control 
     */
    public function getCategoryid() {
        return $this->categoryid;
    }

    /**
     * Set typeid
     *
     * @param \CsnCms\Entity\Type $typeid
     *
     * @return Company
     */
    public function setTypeid(\CsnCms\Entity\Type $typeid = null) {
        $this->typeid = $typeid;

        return $this;
    }

    /**
     * Get typeid
     *
     * @return \CsnCms\Entity\Type 
     */
    public function getTypeid() {
        return $this->typeid;
    }

}
