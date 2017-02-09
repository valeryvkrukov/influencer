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
			if (isset($data['posts'])) {
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
					} elseif (isset($item['place'])) {
						$tpl = 'https://maps.googleapis.com/maps/api/staticmap?center=%s,%s&zoom=13&size=640x400&markers=color:red|label:C|%s,%s&key=%s';
						$url = sprintf($tpl, $item['place']['location']['latitude'], $item['place']['location']['longitude'], $item['place']['location']['latitude'], $item['place']['location']['longitude'], 'AIzaSyAAs3PqhsyoRS39Pk9UXd2tV0ZaEZRxZJQ');
						$feed->setPicture($url);
					}
					if (isset($item['message'])) {
						$feed->setContents($item['message']);
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
			}
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
				$feed->setTitle($item['snippet']['title']);
				if (isset($item['snippet']['standard']['url'])) {
					$feed->setPicture($item['snippet']['standard']['url']);
				}
				$feed->setContents($item['snippet']['description']);
				$feed->setLikes(0);
				$feed->setComments(0);
				$feed->setLink('#');
				$feed->setCreatedAt($item['snippet']['publishedAt']);
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
			foreach ($data as $item) {
				if (isset($this->exists[$item['id']])) {
					$feed = $this->exists[$item['id']];
				} else {
					$feed = new Feed();
					$feed->setInternalId($item['id']);
					$feed->setNetwork('twitter');
				}
				//$feed->setTitle($item['title']);
				if (isset($item['entities']['media'][0]['media_url'])) {
					$feed->setPicture($item['entities']['media'][0]['media_url']);
				}
				$feed->setContents($item['text']);
				$feed->setLikes(intval($item['favorite_count']));
				$feed->setComments(intval($item['retweet_count']));
				$feed->setLink('#');
				$feed->setCreatedAt($item['created_at']);
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