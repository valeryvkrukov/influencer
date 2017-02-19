<?php 
namespace Influencer\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="audience")
 */
class Audience
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
	 * @ORM\Column(name="icon", type="string", nullable=true)
	 */
	protected $icon;
	
	/**
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="audience")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
	 */
	protected $user;
	
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
	public function getIcon() {
		return $this->icon;
	}
	
	/**
	 *
	 * @param unknown_type $icon        	
	 */
	public function setIcon($icon) {
		$this->icon = $icon;
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
	
	
}