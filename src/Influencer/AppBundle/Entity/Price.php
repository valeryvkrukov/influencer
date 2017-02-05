<?php 
namespace Influencer\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="prices")
 */
class Price
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/**
	 * @ORM\Column(name="event_name", type="string", nullable=false)
	 */
	protected $name;
	
	/**
	 * @ORM\Column(name="event_cost", type="string", nullable=false)
	 */
	protected $cost;
	
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
	
	/**
	 *
	 * @return the unknown_type
	 */
	public function getCost() {
		return $this->cost;
	}
	
	/**
	 *
	 * @param unknown_type $cost        	
	 */
	public function setCost($cost) {
		$this->cost = $cost;
		return $this;
	}
	
}