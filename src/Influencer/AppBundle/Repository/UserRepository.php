<?php 
namespace Influencer\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

use Influencer\AppBundle\Entity\User;
use Influencer\AppBundle\Entity\Language;
use Influencer\AppBundle\Entity\Country;
use Influencer\AppBundle\Entity\Audience;
use Influencer\AppBundle\Entity\Price;
use Influencer\AppBundle\Entity\Network;

class UserRepository extends EntityRepository
{
	public function findUserByNetworkId($network, $id)
	{
		$em = $this->getEntityManager();
		$dql = sprintf('SELECT u FROM InfluencerAppBundle:User u LEFT JOIN u.%s AS n WHERE n.userId = :id AND n.code = :network', $network);
		$user = $em->createQuery($dql)->setParameters([
			'id' => $id,
			'network' => $network,
		])->getResult();
		return $user;
	}
	
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
			$em->flush();
			if (isset($data->socials)) {
				$socials = json_decode(json_encode($data->socials), true);
				if (is_array($socials) && sizeof($socials) > 0) {
					foreach ($socials as $network => $item) {
						$setter = 'set'.ucfirst($network);
						//$getter = 'load'.ucfirst($network).'Feed';
						$_network = new Network();
						$_network->setCode($network);
						$_network->setName($network);
						$_network->setUserId($item['id']);
						$_network->setUserName('username');
						$_network->setToken($item['token']);
						$em->persist($_network);
						//$user->$setter($item['id']);
						$user->$setter($_network);
						$getter = 'load'.ucfirst($network).'Feed';
						//$data = $this->get('app.feed_loader')->$getter($item['token'], $item['id']);
						//$em->getRepository('InfluencerAppBundle:Feed')->loadLatestForUser($data, $network, $user->getId());
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
	
	public function addIfNotExists($id, $field, $values, $serializer)
	{
		$em = $this->getEntityManager();
		$user = $this->findOneById($id);
		if (sizeof($values) > 0) {
			$dql = 'DELETE FROM InfluencerAppBundle:%s i WHERE i.user = :user';
			switch($field) {
				case 'languages':
					$dql = sprintf($dql, 'Language');
					break;
				case 'countries':
					$dql = sprintf($dql, 'Country');
					break;
				case 'audience':
					$dql = sprintf($dql, 'Audience');
					break;
				case 'prices':
					$dql = sprintf($dql, 'Price');
					break;
			}
			try {
				$em->createQuery($dql)->setParameter('user', $user)->getResult();
				$em->flush();
			} catch(\Exception $e) {
				var_dump($e->getMessage());die();
			}
			if (sizeof($values) > 0) {
				foreach ($values as $val) {
					switch($field) {
						case 'languages':
							$item = new Language();
							$item->setCode($val->code);
							$item->setName($val->lang);
							$add = 'addLanguage';
							break;
						case 'countries':
							$item = new Country();
							$item->setCode($val->code);
							$item->setName($val->country);
							$add = 'addCountry';
							break;
						case 'audience':
							$item = new Audience();
							$item->setCode($val->tag);
							$item->setName($val->name);
							$add = 'addAudience';
							break;
						case 'prices':
							$item = new Price();
							$item->setTag($val->tag);
							$item->setName($val->name);
							if (isset($item->icon)) {
								$item->setIcon($val->icon);
							}
							$item->setCost($val->cost);
							$add = 'addPrice';
							break;
					}
					if (isset($item)) {
						$item->setUser($user);
						$em->persist($item);
						$user->$add($item);
						$em->persist($user);
					}
				}
				$em->flush();
			}
		}
	}
	
	public function getAllUsers()
	{
		$em = $this->getEntityManager();
		$dql = 'SELECT u.';
	}
	
}