<?php 
namespace Influencer\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="languages")
 */
class Language
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
	
}