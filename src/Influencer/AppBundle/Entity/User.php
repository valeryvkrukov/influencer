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
	 * @ORM\Column(name="age", type="integer", nullable=true)
	 */
	protected $age;
	
	/**
	 * @ORM\Column(name="age_bracket", type="string", nullable=true)
	 */
	protected $ageBracket;
	
	/**
	 * @ORM\Column(name="gender", type="string", nullable=true)
	 */
	protected $gender;
	
	/**
	 * @ORM\Column(name="brief", type="text", nullable=true)
	 */
	protected $brief;
	
	/**
	 * @ORM\Column(name="website", type="string", nullable=true)
	 */
	protected $website;
	
	/**
	 * @ORM\OneToMany(targetEntity="Language", mappedBy="user", cascade={"persist", "remove"})
	 */
	protected $languages;
	
	/**
	 * @ORM\OneToMany(targetEntity="Country", mappedBy="user", cascade={"persist", "remove"})
	 */
	protected $countries;
	
	/**
	 * @ORM\OneToMany(targetEntity="Audience", mappedBy="user", cascade={"persist", "remove"})
	 */
	protected $audience;
	
	/**
	 * @ORM\OneToMany(targetEntity="Price", mappedBy="user", cascade={"persist", "remove"})
	 */
	protected $prices;
	
	/**
	 * @ORM\OneToMany(targetEntity="Feed", mappedBy="user", cascade={"persist", "remove"})
	 */
	protected $feeds;
	
	/**
	 * @ORM\Column(name="frequency", type="string", nullable=true)
	 */
	protected $frequency;

	/**
	 * @ORM\OneToOne(targetEntity="Network", cascade={"persist", "remove"})
	 * @ORM\JoinColumn(name="facebook_id", referencedColumnName="id")
	 */
	protected $facebook;
	
	/**
	 * @ORM\OneToOne(targetEntity="Network", cascade={"persist", "remove"})
	 * @ORM\JoinColumn(name="google_id", referencedColumnName="id")
	 */
	protected $google;

	/**
	 * @ORM\OneToOne(targetEntity="Network", cascade={"persist", "remove"})
	 * @ORM\JoinColumn(name="twitter_id", referencedColumnName="id")
	 */
	protected $twitter;

	/**
	 * @ORM\OneToOne(targetEntity="Network", cascade={"persist", "remove"})
	 * @ORM\JoinColumn(name="instagram_id", referencedColumnName="id")
	 */
	protected $instagram;
	
	/**
	 * @ORM\Column(name="klout", type="string", nullable=true)
	 */
	protected $klout;
	
	/**
	 * @ORM\OneToOne(targetEntity="UserStatistics", mappedBy="user", cascade={"persist", "remove"})
	 */
	protected $statistics;
	
	private $rootDir;
	
	public function __construct($rootDir)
	{
		parent::__construct();
		
		$this->rootDir = realpath(__DIR__.'/../../../../');
		
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
		$splited = explode(',', $profileImage);
		if (isset($splited[1])) {
			$filename = '/uploads/'.md5('profile-image'.$this->getId().$this->getUsername().time());
			$mime = $splited[0];
			$mime_split_without_base64=explode(';', $mime, 2);
			$mime_split=explode('/', $mime_split_without_base64[0], 2);
			if (count($mime_split) == 2) {
				$ext = $mime_split[1];
				$filename .= '.'.$ext;
			}
			$data = $splited[1];
			$fp = fopen(__DIR__.'/../../../../web'.$filename, 'wb');
			fwrite($fp, base64_decode($data));
			fclose($fp);
			$this->profileImage = $filename;
		}
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
		$splited = explode(',', $profileCover);
		if (isset($splited[1])) {
			$filename = '/uploads/'.md5('profile-cover'.$this->getId().$this->getUsername().time());
			$mime = $splited[0];
			$mime_split_without_base64=explode(';', $mime, 2);
			$mime_split=explode('/', $mime_split_without_base64[0], 2);
			if (isset($mime_split[1])) {
				$ext = $mime_split[1];
				$filename .= '.'.$ext;
			}
			$data = $splited[1];
			$fp = fopen(__DIR__.'/../../../../web'.$filename, 'wb');
			fwrite($fp, base64_decode($data));
			fclose($fp);
			$this->profileCover = $filename;
		}
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
	public function getAgeBracket() {
		return $this->ageBracket;
	}
	
	/**
	 *
	 * @param unknown_type $ageBracket        	
	 */
	public function setAgeBracket($ageBracket) {
		$this->ageBracket = $ageBracket;
		return $this;
	}
	
	/**
	 *
	 * @return the unknown_type
	 */
	public function getGender() {
		return $this->gender;
	}
	
	/**
	 *
	 * @param unknown_type $gender        	
	 */
	public function setGender($gender) {
		$this->gender = $gender;
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
		if ($facebook instanceof Network) {
			$this->facebook = $facebook;
		}
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
		if ($google instanceof Network) {
			$this->google = $google;
		}
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
		if ($twitter instanceof Network) {
			$this->twitter = $twitter;
		}
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
		if ($instagram instanceof Network) {
			$this->instagram = $instagram;
		}
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
	
	/**
	 *
	 * @return the unknown_type
	 */
	public function getStatistics() {
		return $this->statistics;
	}
	
	/**
	 *
	 * @param unknown_type $statistics        	
	 */
	public function setStatistics($statistics) {
		$this->statistics = $statistics;
		return $this;
	}
	
}