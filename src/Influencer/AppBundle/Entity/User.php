<?php 
namespace Influencer\AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="Influencer\AppBundle\Repository\UserRepository")
 */
class User extends BaseUser
{
	/**
	 * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/**
	 * @ORM\Column(name="profile_image", type="text", nullable=true)
	 */
	protected $profileImage;
	
	/**
	 * @ORM\Column(name="profile_cover", type="text", nullable=true)
	 */
	protected $profileCover;
	
	/**
	 * @ORM\Column(name="first_name", type="string", nullable=true)
	 */
	protected $firstName;

	/**
	 * @ORM\Column(name="last_name", type="string", nullable=true)
	 */
	protected $lastName;
	
	/**
	 * @ORM\Column(name="contact_number", type="string", nullable=true)
	 */
	protected $contactNumber;
	
	/**
	 * @ORM\Column(name="secondary_number", type="string", nullable=true)
	 */
	protected $secondaryNumber;
	
	/**
	 * @ORM\Column(name="age", type="string", nullable=true)
	 */
	protected $age;
	
	/**
	 * @ORM\Column(name="brief", type="text", nullable=true)
	 */
	protected $brief;
	
	/**
	 * @ORM\Column(name="website", type="string", nullable=true)
	 */
	protected $website;
	
	/**
	 * @ORM\ManyToMany(targetEntity="Language")
	 * @ORM\JoinTable(name="users_languages",
	 *   joinColumns={
	 *     @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 *   },
	 *   inverseJoinColumns={
	 *     @ORM\JoinColumn(name="language_id", referencedColumnName="id")
	 *   }
	 * )
	 */
	protected $languages;
	
	/**
	 * @ORM\ManyToMany(targetEntity="Country")
	 * @ORM\JoinTable(name="users_countries",
	 *   joinColumns={
	 *     @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 *   },
	 *   inverseJoinColumns={
	 *     @ORM\JoinColumn(name="country_id", referencedColumnName="id")
	 *   }
	 * )
	 */
	protected $countries;
	
	/**
	 * @ORM\ManyToMany(targetEntity="Audience")
	 * @ORM\JoinTable(name="users_audience",
	 *   joinColumns={
	 *     @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 *   },
	 *   inverseJoinColumns={
	 *     @ORM\JoinColumn(name="audience_id", referencedColumnName="id")
	 *   }
	 * )
	 */
	protected $audience;
	
	/**
	 * @ORM\ManyToMany(targetEntity="Price")
	 * @ORM\JoinTable(name="users_prices",
	 *   joinColumns={
	 *     @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 *   },
	 *   inverseJoinColumns={
	 *     @ORM\JoinColumn(name="price_id", referencedColumnName="id")
	 *   }
	 * )
	 */
	protected $prices;
	
	/**
	 * @ORM\OneToMany(targetEntity="Feed", mappedBy="user")
	 */
	protected $feeds;
	
	/**
	 * @ORM\Column(name="frequency", type="string", nullable=true)
	 */
	protected $frequency;

	/**
	 * @ORM\Column(name="facebook", type="string", nullable=true)
	 */
	protected $facebook;
	
	/**
	 * @ORM\Column(name="google", type="string", nullable=true)
	 */
	protected $google;

	/**
	 * @ORM\Column(name="twitter", type="string", nullable=true)
	 */
	protected $twitter;

	/**
	 * @ORM\Column(name="instagram", type="string", nullable=true)
	 */
	protected $instagram;
	
	/**
	 * @ORM\Column(name="klout", type="string", nullable=true)
	 */
	protected $klout;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->languages = new \Doctrine\Common\Collections\ArrayCollection();
		$this->countries = new \Doctrine\Common\Collections\ArrayCollection();
		$this->audience = new \Doctrine\Common\Collections\ArrayCollection();
		$this->prices = new \Doctrine\Common\Collections\ArrayCollection();
		$this->feeds = new \Doctrine\Common\Collections\ArrayCollection();
	}
	
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
	public function getProfileImage() {
		return $this->profileImage;
	}
	
	/**
	 *
	 * @param unknown_type $profileImage        	
	 */
	public function setProfileImage($profileImage) {
		$this->profileImage = $profileImage;
		return $this;
	}
	
	/**
	 *
	 * @return the unknown_type
	 */
	public function getProfileCover() {
		return $this->profileCover;
	}
	
	/**
	 *
	 * @param unknown_type $profileCover        	
	 */
	public function setProfileCover($profileCover) {
		$this->profileCover = $profileCover;
		return $this;
	}
				
	/**
	 *
	 * @return the unknown_type
	 */
	public function getFirstName() {
		return $this->firstName;
	}
	
	/**
	 *
	 * @param unknown_type $firstName        	
	 */
	public function setFirstName($firstName) {
		$this->firstName = $firstName;
		return $this;
	}
	
	/**
	 *
	 * @return the unknown_type
	 */
	public function getLastName() {
		return $this->lastName;
	}
	
	/**
	 *
	 * @param unknown_type $lastName        	
	 */
	public function setLastName($lastName) {
		$this->lastName = $lastName;
		return $this;
	}
	
	/**
	 *
	 * @return the unknown_type
	 */
	public function getContactNumber() {
		return $this->contactNumber;
	}
	
	/**
	 *
	 * @param unknown_type $contactNumber        	
	 */
	public function setContactNumber($contactNumber) {
		$this->contactNumber = $contactNumber;
		return $this;
	}
	
	/**
	 *
	 * @return the unknown_type
	 */
	public function getSecondaryNumber() {
		return $this->secondaryNumber;
	}
	
	/**
	 *
	 * @param unknown_type $secondaryNumber        	
	 */
	public function setSecondaryNumber($secondaryNumber) {
		$this->secondaryNumber = $secondaryNumber;
		return $this;
	}
	
	/**
	 *
	 * @return the unknown_type
	 */
	public function getAge() {
		return $this->age;
	}
	
	/**
	 *
	 * @param unknown_type $age        	
	 */
	public function setAge($age) {
		$this->age = $age;
		return $this;
	}
		
	/**
	 *
	 * @return the unknown_type
	 */
	public function getBrief() {
		return $this->brief;
	}
	
	/**
	 *
	 * @param unknown_type $brief        	
	 */
	public function setBrief($brief) {
		$this->brief = $brief;
		return $this;
	}
	
	/**
	 *
	 * @return the unknown_type
	 */
	public function getWebsite() {
		return $this->website;
	}
	
	/**
	 *
	 * @param unknown_type $website        	
	 */
	public function setWebsite($website) {
		$this->website = $website;
		return $this;
	}
	
	/**
	 *
	 * @return the unknown_type
	 */
	public function getLanguages() {
		return $this->languages;
	}
	
	/**
	 *
	 * @param unknown_type $languages        	
	 */
	public function addLanguage($language) {
		$this->languages->add($language);
		return $this;
	}
	
	/**
	 *
	 * @param unknown_type $languages
	 */
	public function removeLanguage($language) {
		$this->languages->removeElement($language);
		return $this;
	}
	
	/**
	 *
	 * @return the unknown_type
	 */
	public function getCountries() {
		return $this->countries;
	}
	
	/**
	 *
	 * @param unknown_type $countries        	
	 */
	public function addCountry($country) {
		$this->countries->add($country);
		return $this;
	}
	
	/**
	 *
	 * @param unknown_type $countries
	 */
	public function removeCountry($country) {
		$this->countries->removeElement($country);
		return $this;
	}
	
	/**
	 *
	 * @return the unknown_type
	 */
	public function getAudience() {
		return $this->audience;
	}
	
	/**
	 *
	 * @param unknown_type $audience        	
	 */
	public function addAudience($audience) {
		$this->audience->add($audience);
		return $this;
	}
	
	/**
	 *
	 * @param unknown_type $audience
	 */
	public function removeAudience($audience) {
		$this->audience->removeElement($audience);
		return $this;
	}
	
	/**
	 *
	 * @return the unknown_type
	 */
	public function getPrices() {
		return $this->prices;
	}
	
	/**
	 *
	 * @param unknown_type $prices        	
	 */
	public function addPrice($price) {
		$this->prices->add($price);
		return $this;
	}
	
	/**
	 *
	 * @param unknown_type $prices
	 */
	public function removePrice($price) {
		$this->prices->removeElement($price);
		return $this;
	}
	
	/**
	 *
	 * @return the unknown_type
	 */
	public function getFeeds() {
		return $this->feeds;
	}
	
	/**
	 *
	 * @param unknown_type $feeds        	
	 */
	public function addFeed($feed) {
		$this->feeds->add($feed);
		return $this;
	}
	
	/**
	 *
	 * @param unknown_type $feeds
	 */
	public function removeFeed($feed) {
		$this->feeds->removeElement($feed);
		return $this;
	}
		
	/**
	 *
	 * @return the unknown_type
	 */
	public function getFrequency() {
		return $this->frequency;
	}
	
	/**
	 *
	 * @param unknown_type $frequency        	
	 */
	public function setFrequency($frequency) {
		$this->frequency = $frequency;
		return $this;
	}
		
	/**
	 *
	 * @return the unknown_type
	 */
	public function getFacebook() {
		return $this->facebook;
	}
	
	/**
	 *
	 * @param unknown_type $facebook        	
	 */
	public function setFacebook($facebook) {
		$this->facebook = $facebook;
		return $this;
	}
	
	/**
	 *
	 * @return the unknown_type
	 */
	public function getGoogle() {
		return $this->google;
	}
	
	/**
	 *
	 * @param unknown_type $google        	
	 */
	public function setGoogle($google) {
		$this->google = $google;
		return $this;
	}
	
	/**
	 *
	 * @return the unknown_type
	 */
	public function getTwitter() {
		return $this->twitter;
	}
	
	/**
	 *
	 * @param unknown_type $twitter        	
	 */
	public function setTwitter($twitter) {
		$this->twitter = $twitter;
		return $this;
	}
	
	/**
	 *
	 * @return the unknown_type
	 */
	public function getInstagram() {
		return $this->instagram;
	}
	
	/**
	 *
	 * @param unknown_type $instagram        	
	 */
	public function setInstagram($instagram) {
		$this->instagram = $instagram;
		return $this;
	}
	
	/**
	 *
	 * @return the unknown_type
	 */
	public function getKlout() {
		return $this->klout;
	}
	
	/**
	 *
	 * @param unknown_type $klout        	
	 */
	public function setKlout($klout) {
		$this->klout = $klout;
		return $this;
	}
	
	
	
}