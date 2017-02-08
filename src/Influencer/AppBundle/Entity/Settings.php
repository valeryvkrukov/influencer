<?php 
namespace Influencer\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="settings")
 * @ORM\Entity(repositoryClass="Influencer\AppBundle\Repository\SettingsRepository")
 */
class Settings
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/**
	 * @ORM\Column(name="option", type="string", nullable=false)
	 */
	protected $option;

	/**
	 * @ORM\Column(name="option_type", type="string", nullable=false)
	 */
	protected $optionType;

	/**
	 * @ORM\Column(name="value", type="text", nullable=true)
	 */
	protected $value;
	
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
	public function getOption() {
		return $this->option;
	}
	
	/**
	 *
	 * @param unknown_type $option        	
	 */
	public function setOption($option) {
		$this->option = $option;
		return $this;
	}
	
	/**
	 *
	 * @return the unknown_type
	 */
	public function getOptionType() {
		return $this->optionType;
	}
	
	/**
	 *
	 * @param unknown_type $optionType        	
	 */
	public function setOptionType($optionType) {
		$this->optionType = $optionType;
		return $this;
	}
		
	/**
	 *
	 * @return the unknown_type
	 */
	public function getValue() {
		return $this->value;
	}
	
	/**
	 *
	 * @param unknown_type $value        	
	 */
	public function setValue($value) {
		$this->value = $value;
		return $this;
	}
	
}