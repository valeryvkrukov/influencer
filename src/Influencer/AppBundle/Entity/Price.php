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
	 * @ORM\Column(name="event_tag", type="string", nullable=false)
	 */
	protected $tag;
	
	/**
	 * @ORM\Column(name="event_icon", type="string", nullable=true)
	 */
	protected $icon;
	
	/**
	 * @ORM\Column(name="event_cost", type="string", nullable=false)
	 */
	protected $cost;
	
	/**
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="prices")
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