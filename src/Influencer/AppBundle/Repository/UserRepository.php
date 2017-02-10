<?php 
namespace Influencer\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

use Influencer\AppBundle\Entity\User;
use Influencer\AppBundle\Entity\Language;
use Influencer\AppBundle\Entity\Country;
use Influencer\AppBundle\Entity\Audience;
use Influencer\AppBundle\Entity\Price;

class UserRepository extends EntityRepository
{
	public function findByOrCondition($params)
	{
		$em = $this->getEntityManager();
		$dql = 'SELECT u FROM InfluencerAppBundle:User u WHERE ';
		$conditioins = [];
		foreach ($params as $field => $value) {
			$conditioins[] = sprintf('u.%s = :%s', $field, $field);
		}
		$dql .= implode(' OR ', $conditioins);
		return $em->createQuery($dql)->setParameters($params)->getOneOrNullResult();
	}
	
	public function checkForUniqueUser($username, $email)
	{
		$em = $this->getEntityManager();
		$user = $em->createQuery('SELECT u.id FROM InfluencerAppBundle:User u WHERE u.username = :username OR u.email = :email')
			->setParameters([
				'username' => $username,
				'email' => $email,
			])
			->getOneOrNullResult();
		return ($user === null);
	}
	
	public function getUserSocialNetworkId($userId, $network)
	{
		$em = $this->getEntityManager();
		$user = $this->find($userId);
		$getter = 'get'.ucfirst($network);
		return $user->$getter();
	}
	
	public function createInfluencerUser($data, $feedLoader)
	{
		try {
			$em = $this->getEntityManager();
			$user = new User();
			$user->setUsername($data->username);
			if (isset($data->profileImage)) {
				$user->setProfileImage($data->profileImage);
			}
			$user->setEmail($data->email);
			$user->setPlainPassword('B!e281ckr');
			$user->setFirstName($data->first_name);
			$user->setLastName($data->last_name);
			$user->setContactNumber($data->contact_number);
			$tz = new \DateTimeZone(date_default_timezone_get());
			$age = \DateTime::createFromFormat('d/m/Y', $data->dob, $tz)->diff(new \DateTime('now', $tz))->y;
			$user->setAge($age);
			if (isset($data->secondary_number)) {
				$user->setSecondaryNumber($data->secondary_number);
			}
			$user->setBrief($data->bio);
			if ($data->website) {
				$user->setWebsite($data->website);
			}
			if ($data->frequency) {
				$user->setFrequency($data->frequency);
			}
			if (is_array($data->languages) && sizeof($data->languages) > 0) {
				foreach ($data->languages as $item) {
					$language = new Language();
					$language->setCode($item->code);
					$language->setName($item->lang);
					$language->setUser($user);
					$em->persist($language);
					$user->addLanguage($language);
				}
			}
			if (is_array($data->countries) && sizeof($data->countries) > 0) {
				foreach ($data->countries as $item) {
					$country = new Country();
					$country->setCode($item->code);
					$country->setName($item->country);
					$country->setUser($user);
					$em->persist($country);
					$user->addCountry($country);
				}
			}
			if (is_array($data->categories) && sizeof($data->categories) > 0) {
				foreach ($data->categories as $item) {
					$audience = new Audience();
					$audience->setCode($item->tag);
					$audience->setName($item->name);
					$audience->setUser($user);
					$em->persist($audience);
					$user->addAudience($audience);
				}
			}
			if (isset($data->prices)) {
				$prices = json_decode(json_encode($data->prices), true);
				$types = [
					'event' => [
						'name' => 'Event',
						'icon' => 'pg-calender',
					],
					'video' => [
						'name' => 'Video',
						'icon' => 'pg-movie',
					],
					'photo' => [
						'name' => 'Photoshoot',
						'icon' => 'pg-camera',
					],
					'social' => [
						'name' => 'Social media',
						'icon' => 'pg-social',
					],
				];
				foreach ($prices as $event => $cost) {
					$price = new Price();
					$price->setName($types[$event]['name']);
					$price->setTag($event);
					$price->setIcon($types[$event]['icon']);
					$price->setCost($cost);
					$price->setUser($user);
					$em->persist($price);
					$user->addPrice($price);
				}
			}
			$user->addRole('ROLE_INFLUENCER');
			$user->setEnabled(true);
			$em->persist($user);
			if (isset($data->socials)) {
				$socials = json_decode(json_encode($data->socials), true);
				if (is_array($socials) && sizeof($socials) > 0) {
					foreach ($socials as $network => $item) {
						$setter = 'set'.ucfirst($network);
						//$getter = 'load'.ucfirst($network).'Feed';
						$user->$setter($item['id']);
						
						//$em->getRepository('InfluencerAppBundle:Feed')->loadLatestForUser($data, $network, $user);
						//$em->getRepository('InfluencerAppBundle:Feed')->loadLatestForUser($network, $user, $item['id'], $item['token']);
					}
					$em->persist($user);
				}
			}
			$em->flush();
			return $user;
		} catch(\Exception $e) {
			var_dump($e->getMessage(), $e->getLine());
		}
	}
	
	public function addIfNotExists($id, $field, $values)
	{
		$em = $this->getEntityManager();
		$dql = 'SELECT u ';
	}
	
	public function getAllUsers()
	{
		$em = $this->getEntityManager();
		$dql = 'SELECT u.';
	}
	
}