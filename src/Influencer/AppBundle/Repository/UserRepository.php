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
	
	public function createInfluencerUser($data)
	{
		try {
			$em = $this->getEntityManager();
			$user = new User();
			$user->setUsername($data->username);
			$user->setEmail($data->email);
			$user->setPlainPassword('B!e281ckr');
			$user->setFirstName($data->first_name);
			$user->setLastName($data->last_name);
			$user->setContactNumber($data->contact_number);
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
					$em->persist($language);
					$user->addLanguage($language);
				}
			}
			if (is_array($data->countries) && sizeof($data->countries) > 0) {
				foreach ($data->countries as $item) {
					$country = new Country();
					$country->setCode($item->code);
					$country->setName($item->country);
					$em->persist($country);
					$user->addCountry($country);
				}
			}
			if (is_array($data->audience) && sizeof($data->audience) > 0) {
				foreach ($data->audience as $item) {
					$audience = new Audience();
					$audience->setName($item);
					$em->persist($audience);
					$user->addAudience($audience);
				}
			}
			if (isset($data->prices)) {
				$prices = json_decode(json_encode($data->prices), true);
				foreach ($prices as $event => $cost) {
					$price = new Price();
					$price->setName($event);
					$price->setCost($cost);
					$em->persist($price);
					$user->addPrice($price);
				}
			}
			$user->addRole('ROLE_INFLUENCER');
			$em->persist($user);
			if (isset($data->socials)) {
				$socials = json_decode(json_encode($data->socials), true);
				if (is_array($socials) && sizeof($socials) > 0) {
					foreach ($socials as $network => $item) {
						$setter = 'set'.ucfirst($network);
						$user->$setter($item['id']);
						//$em->getRepository('InfluencerAppBundle:Feed')->loadLatestForUser($network, $user, $item['id'], $item['token']);
					}
					$em->persist($user);
				}
			}
			$em->flush();
			return $user;
		} catch(\Exception $e) {
			var_dump($e->getMessage());
		}
	}
	
}