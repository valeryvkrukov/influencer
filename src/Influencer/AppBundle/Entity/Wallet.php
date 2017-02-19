<?php 
namespace Influencer\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="wallets")
 * @ORM\Entity(repositoryClass="Influencer\AppBundle\Repository\WalletRepository")
 */
class Wallet
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/**
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="wallet")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
	 */
	protected $user;
	
	/**
	 * @ORM\Column(name="balance", type="float", nullable=true)
	 */
	protected $balance;

	/**
	 * @ORM\Column(name="escrow", type="float", nullable=true)
	 */
	protected $escrow;
	
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
	public function getBalance() {
		return $this->balance;
	}
	
	/**
	 *
	 * @param unknown_type $balance        	
	 */
	public function setBalance($balance) {
		$this->balance = $balance;
		return $this;
	}
	
	/**
	 *
	 * @return the unknown_type
	 */
	public function getEscrow() {
		return $this->escrow;
	}
	
	/**
	 *
	 * @param unknown_type $escrow        	
	 */
	public function setEscrow($escrow) {
		$this->escrow = $escrow;
		return $this;
	}
	
	
}