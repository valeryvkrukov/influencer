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