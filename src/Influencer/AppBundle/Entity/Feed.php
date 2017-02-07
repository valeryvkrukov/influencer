<?php 
namespace Influencer\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="feeds")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="Influencer\AppBundle\Repository\FeedRepository")
 */
class Feed
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @ORM\Column(name="internal_id", type="string", nullable=false)
	 */
	protected $internalId;
	
	/**
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="feeds")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 */
	protected $user;
	
	/**
	 * @ORM\Column(name="network", type="string", nullable=false)
	 */
	protected $network;
	
	/**
	 * @ORM\Column(name="title", type="string", nullable=true)
	 */
	protected $title;
	
	/**
	 * @ORM\Column(name="picture", type="string", nullable=true)
	 */
	protected $picture;
	
	/**
	 * @ORM\Column(name="contents", type="text", nullable=true)
	 */
	protected $contents;
	
	/**
	 * @ORM\Column(name="link", type="string", nullable=true)
	 */
	protected $link;
	
	/**
	 * @ORM\Column(name="likes", type="integer", nullable=true)
	 */
	protected $likes;
	
	/**
	 * @ORM\Column(name="comments", type="integer", nullable=true)
	 */
	protected $comments;
	
	/**
	 * @ORM\Column(name="added_at", type="datetime")
	 */
	protected $addedAt;
	
	/**
	 * @ORM\Column(name="created_at", type="datetime", nullable=true)
	 */
	protected $createdAt;
	
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
	public function getInternalId() {
		return $this->internalId;
	}
	
	/**
	 *
	 * @param unknown_type $internalId        	
	 */
	public function setInternalId($internalId) {
		$this->internalId = $internalId;
		return $this;
	}
	
	/**
	 *
	 * @return the unknown_type
	 */
	public function getUser() {
		return $this->user;
	}
	
	/**
	 *
	 * @param unknown_type $user        	
	 */
	public function setUser($user) {
		$this->user = $user;
		return $this;
	}
			
	/**
	 *
	 * @return the unknown_type
	 */
	public function getNetwork() {
		return $this->network;
	}
	
	/**
	 *
	 * @param unknown_type $network        	
	 */
	public function setNetwork($network) {
		$this->network = $network;
		return $this;
	}
	
	/**
	 *
	 * @return the unknown_type
	 */
	public function getTitle() {
		return $this->title;
	}
	
	/**
	 *
	 * @param unknown_type $title        	
	 */
	public function setTitle($title) {
		$this->title = $title;
		return $this;
	}
	
	/**
	 *
	 * @return the unknown_type
	 */
	public function getPicture() {
		return $this->picture;
	}
	
	/**
	 *
	 * @param unknown_type $picture        	
	 */
	public function setPicture($picture) {
		$this->picture = $picture;
		return $this;
	}
		
	/**
	 *
	 * @return the unknown_type
	 */
	public function getContents() {
		return $this->contents;
	}
	
	/**
	 *
	 * @param unknown_type $contents        	
	 */
	public function setContents($contents) {
		$this->contents = $contents;
		return $this;
	}
	
	/**
	 *
	 * @return the unknown_type
	 */
	public function getLink() {
		return $this->link;
	}
	
	/**
	 *
	 * @param unknown_type $link        	
	 */
	public function setLink($link) {
		$this->link = $link;
		return $this;
	}
	
	/**
	 *
	 * @return the unknown_type
	 */
	public function getLikes() {
		return $this->likes;
	}
	
	/**
	 *
	 * @param unknown_type $likes        	
	 */
	public function setLikes($likes) {
		$this->likes = $likes;
		return $this;
	}
	
	/**
	 *
	 * @return the unknown_type
	 */
	public function getComments() {
		return $this->comments;
	}
	
	/**
	 *
	 * @param unknown_type $comments        	
	 */
	public function setComments($comments) {
		$this->comments = $comments;
		return $this;
	}
		
	/**
	 *
	 * @return the unknown_type
	 */
	public function getAddedAt() {
		return $this->addedAt;
	}
	
	/**
	 *
	 * @ORM\PrePersist()
	 */
	public function setAddedAt() {
		$this->addedAt = new \DateTime();
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
	 * @param unknown_type $createdAt        	
	 */
	public function setCreatedAt($createdAt) {
		$this->createdAt = new \DateTime($createdAt);
		return $this;
	}
	
	
	
}