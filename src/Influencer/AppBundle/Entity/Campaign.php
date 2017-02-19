<?php 
namespace Influencer\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="campaigns")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="Influencer\AppBundle\Repository\CampaignRepository")
 */
class Campaign
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/**
	 * @ORM\ManyToOne(targetEntity="User")
	 * @ORM\JoinColumn(name="client_id", referencedColumnName="id")
	 */
	protected $client;
	
	/**
	 * @ORM\ManyToMany(targetEntity="User")
	 * @ORM\JoinTable(name="campaigns_influencers",
	 *   joinColumns={
	 *     @ORM\JoinColumn(name="influencer_id", referencedColumnName="id")
	 *   },
	 *   inverseJoinColumns={
	 *     @ORM\JoinColumn(name="campaign_id", referencedColumnName="id")
	 *   }
	 * )
	 */
	protected $influencers;
	
	/**
	 * @ORM\Column(name="name", type="string", nullable=false)
	 */
	protected $name;
	
	/**
	 * @ORM\Column(name="brand", type="string", nullable=true)
	 */
	protected $brand;
	
	/**
	 * @ORM\Column(name="location", type="string", nullable=false)
	 */
	protected $location;
	
	/**
	 * @ORM\Column(name="budget", type="float", nullable=true)
	 */
	protected $budget;
	
	/**
	 * @ORM\Column(name="target", type="string", nullable=true)
	 */
	protected $target;
	
	/**
	 * @ORM\Column(name="deadline", type="datetime", nullable=false)
	 */
	protected $deadline;
	
	/**
	 * @ORM\Column(name="description", type="text", nullable=false)
	 */
	protected $description;
	
	/**
	 * @ORM\Column(name="language", type="string", nullable=false)
	 */
	protected $language;
	
	/**
	 * @ORM\Column(name="status", type="string", nullable=false)
	 */
	protected $status;
	
	/**
	 * @ORM\Column(name="files", type="array", nullable=true)
	 */
	protected $files;
	
	/**
	 * @ORM\Column(name="formats", type="array", nullable=true)
	 */
	protected $formats;
	
	/**
	 * @ORM\Column(name="tag", type="string", nullable=true)
	 */
	protected $tag;
	
	/**
	 * @ORM\Column(name="caption", type="string", nullable=true)
	 */
	protected $caption;
	
	/**
	 * @ORM\Column(name="emojii", type="string", nullable=true)
	 */
	protected $emojii;
	
	/**
	 * @ORM\Column(name="restrictions", type="text", nullable=true)
	 */
	protected $restrictions;
	
	/**
	 * @ORM\Column(name="created_at", type="datetime")
	 */
	protected $createdAt;
	
	public function __construct()
	{
		$this->influencers = new \Doctrine\Common\Collections\ArrayCollection();
	}
	
	/**
	 *
	 * @return the unknown_type
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 *
	 * @return the unknown_type
	 */
	public function getClient() {
		return $this->client;
	}
	
	/**
	 *
	 * @param unknown_type $client        	
	 */
	public function setClient($client) {
		$this->client = $client;
		return $this;
	}
	
	/**
	 *
	 * @return the unknown_type
	 */
	public function getInfluencers() {
		return $this->influencers;
	}
	
	/**
	 *
	 * @param unknown_type $influencers        	
	 */
	public function addInfluencer($influencer) {
		$this->influencers->add($influencer);
		return $this;
	}
	
	/**
	 *
	 * @param unknown_type $influencers
	 */
	public function removeInfluencer($influencer) {
		$this->influencers->removeElement($influencer);
		return $this;
	}
	
	/**
	 *
	 * @return the unknown_type
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 *
	 * @param unknown_type $name        	
	 */
	public function setName($name) {
		$this->name = $name;
		return $this;
	}
	
	/**
	 *
	 * @return the unknown_type
	 */
	public function getBrand() {
		return $this->brand;
	}
	
	/**
	 *
	 * @param unknown_type $brand        	
	 */
	public function setBrand($brand) {
		$this->brand = $brand;
		return $this;
	}
	
	/**
	 *
	 * @return the unknown_type
	 */
	public function getLocation() {
		return $this->location;
	}
	
	/**
	 *
	 * @param unknown_type $location        	
	 */
	public function setLocation($location) {
		$this->location = $location;
		return $this;
	}
	
	/**
	 *
	 * @return the unknown_type
	 */
	public function getBudget() {
		return $this->budget;
	}
	
	/**
	 *
	 * @param unknown_type $budget        	
	 */
	public function setBudget($budget) {
		$this->budget = $budget;
		return $this;
	}
	
	/**
	 *
	 * @return the unknown_type
	 */
	public function getTarget() {
		return $this->target;
	}
	
	/**
	 *
	 * @param unknown_type $target        	
	 */
	public function setTarget($target) {
		$this->target = $target;
		return $this;
	}
	
	/**
	 *
	 * @return the unknown_type
	 */
	public function getDeadline() {
		return $this->deadline;
	}
	
	/**
	 *
	 * @param unknown_type $deadline        	
	 */
	public function setDeadline($deadline) {
		$this->deadline = $deadline;
		return $this;
	}
	
	/**
	 *
	 * @return the unknown_type
	 */
	public function getDescription() {
		return $this->description;
	}
	
	/**
	 *
	 * @param unknown_type $description        	
	 */
	public function setDescription($description) {
		$this->description = $description;
		return $this;
	}
	
	/**
	 *
	 * @return the unknown_type
	 */
	public function getLanguage() {
		return $this->language;
	}
	
	/**
	 *
	 * @param unknown_type $language        	
	 */
	public function setLanguage($language) {
		$this->language = $language;
		return $this;
	}
	
	/**
	 *
	 * @return the unknown_type
	 */
	public function getStatus() {
		return $this->status;
	}
	
	/**
	 *
	 * @param unknown_type $status        	
	 */
	public function setStatus($status) {
		$this->status = $status;
		return $this;
	}
		
	/**
	 *
	 * @return the unknown_type
	 */
	public function getFiles() {
		return $this->files;
	}
	
	/**
	 *
	 * @param unknown_type $files        	
	 */
	public function setFiles($files) {
		$this->files = $files;
		return $this;
	}
	
	/**
	 *
	 * @return the unknown_type
	 */
	public function getFormats() {
		return $this->formats;
	}
	
	/**
	 *
	 * @param unknown_type $formats        	
	 */
	public function setFormats($formats) {
		$this->formats = $formats;
		return $this;
	}
	
	/**
	 *
	 * @return the unknown_type
	 */
	public function getTag() {
		return $this->tag;
	}
	
	/**
	 *
	 * @param unknown_type $tag        	
	 */
	public function setTag($tag) {
		$this->tag = $tag;
		return $this;
	}
	
	/**
	 *
	 * @return the unknown_type
	 */
	public function getCaption() {
		return $this->caption;
	}
	
	/**
	 *
	 * @param unknown_type $caption        	
	 */
	public function setCaption($caption) {
		$this->caption = $caption;
		return $this;
	}
	
	/**
	 *
	 * @return the unknown_type
	 */
	public function getEmojii() {
		return $this->emojii;
	}
	
	/**
	 *
	 * @param unknown_type $emojii        	
	 */
	public function setEmojii($emojii) {
		$this->emojii = $emojii;
		return $this;
	}
	
	/**
	 *
	 * @return the unknown_type
	 */
	public function getRestrictions() {
		return $this->restrictions;
	}
	
	/**
	 *
	 * @param unknown_type $restrictions        	
	 */
	public function setRestrictions($restrictions) {
		$this->restrictions = $restrictions;
		return $this;
	}
	
	/**
	 *
	 * @return the unknown_type
	 */
	public function getCreatedAt() {
		return $this->createdAt;
	}
	
	/**
	 *
	 * @ORM\PrePersist
	 */
	public function setCreatedAt() {
		$this->createdAt = new \DateTime();
		return $this;
	}
	
}