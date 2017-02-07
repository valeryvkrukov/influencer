<?php 
namespace Influencer\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

use GuzzleHttp;
use GuzzleHttp\Subscriber\Oauth\Oauth1;

use Influencer\AppBundle\Entity\Feed;

class FeedRepository extends EntityRepository
{
	protected $exists = [];
	
	public function getUserNetworks($userId)
	{
		$em = $this->getEntityManager();
		$dql = 'SELECT DISTINCT f.network AS network FROM InfluencerAppBundle:Feed f WHERE f.user = :userId';
		$networks = $em->createQuery($dql)->setParameters(['userId' => $userId])->getArrayResult();
		return $networks;
	}
	
	public function loadSavedFeedsFor($userId, $network = null, $hydration = 'object', $limit = null)
	{
		$em = $this->getEntityManager();
		$dql = 'SELECT f FROM InfluencerAppBundle:Feed f WHERE f.user = :userId';
		$params = [
			'userId' => $userId,
		];
		if ($network !== null) {
			$params['network'] = $network;
			$dql .= ' AND f.network = :network';
		}
		$dql .= ' ORDER BY f.createdAt DESC';
		$query = $em->createQuery($dql)->setParameters($params);
		if ($limit !== null) {
			$query->setMaxResults($limit);
		}
		if ($hydration == 'object') {
			$feeds = $query->getResult();
		} else {
			$feeds = $query->getArrayResult();
		}
		return $feeds;
	}
	
	public function loadLatestForUser($data, $network, $userId)
	{
		$em = $this->getEntityManager();
		$loader = 'update'.ucfirst($network);
		$user = $em->getRepository('InfluencerAppBundle:User')->find($userId);
		if ($user) {
			$feeds = $this->loadSavedFeedsFor($userId, $network);
			foreach ($feeds as $item) {
				$this->exists[$item->getInternalId()] = $item;
			}
			$data = $this->$loader($data, $user);
		}
		return $data;
	}
	
	protected function updateFacebook($data, $user)
	{
		$objects = [];
		try {
			$em = $this->getEntityManager();
			foreach ($data['posts']['data'] as $item) {
				if (isset($this->exists[$item['id']])) {
					$feed = $this->exists[$item['id']];
				} else {
					$feed = new Feed();
					$feed->setInternalId($item['id']);
					$feed->setNetwork('facebook');
				}
				if (isset($item['caption'])) {
					$feed->setTitle($item['caption']);
				}
				if (isset($item['picture'])) {
					$feed->setPicture($item['picture']);
				}
				if (isset($item['message'])) {
					$feed->setContents($item['message']);
				} elseif (isset($item['place'])) {
					$feed->setContents(implode(', ', $item['place']['location']));
				}
				$feed->setLikes(0);
				$feed->setComments(0);
				$feed->setLink((isset($item['link'])?$item['link']:'#'));
				$feed->setCreatedAt($item['created_time']);
				$em->persist($feed);
				if (!isset($this->exists[$item['id']])) {
					$feed->setUser($user);
					$em->persist($feed);
					$user->addFeed($feed);
					$em->persist($user);
					$objects[] = $feed;
				}
			}
			$em->flush();
			return $objects;
		} catch(\Exception $e) {
			var_dump($e->getMessage());
		}
	}
	
	protected function updateGoogle($data, $user)
	{
		$objects = [];
		try {
			$em = $this->getEntityManager();
			foreach ($data['items'] as $item) {
				if (isset($this->exists[$item['id']])) {
					$feed = $this->exists[$item['id']];
				} else {
					$feed = new Feed();
					$feed->setInternalId($item['id']);
					$feed->setNetwork('google');
				}
				$feed->setTitle($item['title']);
				if (isset($item['object']['attachments'][0]['image']['url'])) {
					$feed->setPicture($item['object']['attachments'][0]['image']['url']);
				}
				$feed->setContents($item['object']['content']);
				$feed->setLikes(intval($item['object']['plusoners']['totalItems']));
				$feed->setComments(intval($item['object']['replies']['totalItems']));
				$feed->setLink($item['object']['url']);
				$feed->setCreatedAt($item['published']);
				$em->persist($feed);
				if (!isset($this->exists[$item['id']])) {
					$feed->setUser($user);
					$em->persist($feed);
					$user->addFeed($feed);
					$em->persist($user);
					$objects[] = $feed;
				}
			}
			$em->flush();
			return $objects;
		} catch(\Exception $e) {
			var_dump($e->getMessage());
		}
	}
	
	protected function updateTwitter($data, $user)
	{
		$objects = [];
		try {
			$em = $this->getEntityManager();
			foreach ($data['items'] as $item) {
				if (isset($this->exists[$item['id']])) {
					$feed = $this->exists[$item['id']];
				} else {
					$feed = new Feed();
					$feed->setInternalId($item['id']);
					$feed->setNetwork('google');
				}
				$feed->setTitle($item['title']);
				if (isset($item['object']['attachments'][0]['image']['url'])) {
					$feed->setPicture($item['object']['attachments'][0]['image']['url']);
				}
				$feed->setContents($item['object']['content']);
				$feed->setLikes(intval($item['object']['plusoners']['totalItems']));
				$feed->setComments(intval($item['object']['replies']['totalItems']));
				$feed->setLink($item['object']['url']);
				$feed->setCreatedAt($item['published']);
				$em->persist($feed);
				if (!isset($this->exists[$item['id']])) {
					$feed->setUser($user);
					$em->persist($feed);
					$user->addFeed($feed);
					$em->persist($user);
					$objects[] = $feed;
				}
			}
			$em->flush();
			return $objects;
		} catch(\Exception $e) {
			var_dump($e->getMessage());
		}
	}
	
	protected function updateInstagram($data, $user)
	{
		$objects = [];
		try {
			$em = $this->getEntityManager();
			foreach ($data['data'] as $item) {
				if (isset($this->exists[$item['id']])) {
					$feed = $this->exists[$item['id']];
				} else {
					$feed = new Feed();
					$feed->setInternalId($item['id']);
					$feed->setNetwork('instagram');
				}
				$feed->setTitle((isset($item['caption'])?$item['caption']:'Untitled'));
				$feed->setPicture($item['images']['low_resolution']['url']);
				$feed->setContents($item['images']['standard_resolution']['url']);
				$feed->setLikes(intval($item['likes']['count']));
				$feed->setComments(intval($item['comments']['count']));
				$feed->setLink($item['link']);
				$feed->setCreatedAt(date('c', $item['created_time']));
				$em->persist($feed);
				if (!isset($this->exists[$item['id']])) {
					$feed->setUser($user);
					$em->persist($feed);
					$user->addFeed($feed);
					$em->persist($user);
					$objects[] = $feed;
				}
			}
			$em->flush();
			return $objects;
		} catch(\Exception $e) {
			var_dump($e->getMessage());
		}
	}
}