<?php 
namespace Influencer\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="networks")
 */
class Network
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/**
	 * @ORM\Column(name="code", type="string", nullable=false)
	 */
	protected $code;
	
	/**
	 * @ORM\Column(name="name", type="string", nullable=false)
	 */
	protected $name;
	
	/**
	 * @ORM\Column(name="user_id", type="string", nullable=false)
	 */
	protected $userId;
	
	/**
	 * @ORM\Column(name="user_name", type="string", nullable=false)
	 */
	protected $userName;
	
	/**
	 * @ORM\Column(name="token", type="string", nullable=false)
	 */
	protected $token;
	
	/**
	 * @ORM\Column(name="followers", type="integer", nullable=true)
	 */
	protected $followers;
	
	/**
	 * @ORM\Column(name="followers_count", type="integer", nullable=true)
	 */
	protected $followersCount;
	
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
	public function getCode() {
		return $this->code;
	}
	
	/**
	 *
	 * @param unknown_type $code        	
	 */
	public function setCode($code) {
		$this->code = $code;
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
	public function getUserId() {
		return $this->userId;
	}
	
	/**
	 *
	 * @param unknown_type $userId        	
	 */
	public function setUserId($userId) {
		$this->userId = $userId;
		return $this;
	}
	
	/**
	 *
	 * @return the unknown_type
	 */
	public function getUserName() {
		return $this->userName;
	}
	
	/**
	 *
	 * @param unknown_type $userName        	
	 */
	public function setUserName($userName) {
		$this->userName = $userName;
		return $this;
	}
		
	/**
	 *
	 * @return the unknown_type
	 */
	public function getToken() {
		return $this->token;
	}
	
	/**
	 *
	 * @param unknown_type $token        	
	 */
	public function setToken($token) {
		$this->token = $token;
		return $this;
	}
	
}