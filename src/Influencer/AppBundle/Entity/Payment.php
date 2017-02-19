<?php 
namespace Influencer\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="payments")
 */
class Payment
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/**
	 * @ORM\Column(name="amount", type="float", nullable=false)
	 */
	protected $amount;
	
	/**
	 * @ORM\Column(name="type", type="string", nullable=false)
	 */
	protected $type;
	
	/**
	 * @ORM\Column(name="direction", type="string", nullable=false)
	 */
	protected $direction;
	
	/**
	 * @ORM\Column(name="status", type="string", nullable=false)
	 */
	protected $status;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Wallet")
	 * @ORM\JoinColumn(name="wallet_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
	 */
	protected $wallet;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Campaign")
	 * @ORM\JoinColumn(name="campaign_id", referencedColumnName="id")
	 */
	protected $campaign;
	
	/**
	 * @ORM\Column(name="paid_at", type="datetime")
	 */
	protected $paidAt;

	/**
	 * @ORM\Column(name="created_at", type="datetime")
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
	public function getAmount() {
		return $this->amount;
	}
	
	/**
	 *
	 * @param unknown_type $amount        	
	 */
	public function setAmount($amount) {
		$this->amount = $amount;
		return $this;
	}
	
	/**
	 *
	 * @return the unknown_type
	 */
	public function getType() {
		return $this->type;
	}
	
	/**
	 *
	 * @param unknown_type $type        	
	 */
	public function setType($type) {
		$this->type = $type;
		return $this;
	}
	
	/**
	 *
	 * @return the unknown_type
	 */
	public function getDirection() {
		return $this->direction;
	}
	
	/**
	 *
	 * @param unknown_type $direction        	
	 */
	public function setDirection($direction) {
		$this->direction = $direction;
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
	public function getWallet() {
		return $this->wallet;
	}
	
	/**
	 *
	 * @param unknown_type $wallet        	
	 */
	public function setWallet($wallet) {
		$this->wallet = $wallet;
		return $this;
	}
	
	/**
	 *
	 * @return the unknown_type
	 */
	public function getCampaign() {
		return $this->campaign;
	}
	
	/**
	 *
	 * @param unknown_type $campaign        	
	 */
	public function setCampaign($campaign) {
		$this->campaign = $campaign;
		return $this;
	}
	
	/**
	 *
	 * @return the unknown_type
	 */
	public function getPaidAt() {
		return $this->paidAt;
	}
	
	/**
	 * @param unknown_type $paidAt     	
	 */
	public function setPaidAt($paidAt) {
		$this->paidAt = $paidAt;
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
	 * @ORM\PrePersist	
	 */
	public function setCreatedAt() {
		$this->createdAt = new \DateTime();
		return $this;
	}
	
	
}