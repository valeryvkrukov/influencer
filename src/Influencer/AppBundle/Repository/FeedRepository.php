<?php 
namespace Influencer\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

use GuzzleHttp;
use GuzzleHttp\Subscriber\Oauth\Oauth1;

use Influencer\AppBundle\Entity\Feed;

class FeedRepository extends EntityRepository
{
	protected $exists = [];
	
	public function loadSavedFeedsFor($userId, $network = null, $hydration = 'object')
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
		if ($hydration == 'object') {
			$feeds = $em->createQuery($dql)->setParameters($params)->getResult();
		} else {
			$feeds = $em->createQuery($dql)->setParameters($params)->getArrayResult();
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
	}
	
	protected function updateFacebook($data, $user)
	{
		
	}
	
	protected function updateGoogle($data, $user)
	{
		
	}
	
	protected function updateTwitter($data, $user)
	{
	
	}
	
	protected function updateInstagram($data, $user)
	{
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
				}
			}
			$em->flush();
		} catch(\Exception $e) {
			var_dump($e->getMessage());
		}
	}
}