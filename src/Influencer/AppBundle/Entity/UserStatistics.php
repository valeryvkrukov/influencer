<?php 
namespace Influencer\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="users_statistics")
 * @ORM\Entity(repositoryClass="Influencer\AppBundle\Repository\UserStatisticsRepository")
 */
class UserStatistics
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/**
	 * @ORM\OneToOne(targetEntity="User", inversedBy="statistics")
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