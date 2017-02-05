<?php 
namespace Influencer\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

use GuzzleHttp;
use GuzzleHttp\Subscriber\Oauth\Oauth1;

use Influencer\AppBundle\Entity\Feed;

class FeedRepository extends EntityRepository
{
	public function loadLatestForUser($network, $user, $id, $token)
	{
		$loader = 'load'.ucfirst($network);
		$data = $this->$loader($user, $id, $token);
	}
	
	protected function loadFacebook($user, $id, $token)
	{
		$client = new GuzzleHttp\Client();
		$fields = 'name,created_time,description,picture,permalink_url';
		$response = $client->request('GET', 'https://graph.facebook.com/v2.8/me', [
			'query' => [
				'access_token' => $token,
				'fields' => $fields,
			],
		]);
		$posts = json_decode($response->getBody(), true);
		$result = [];
		var_dump($posts);die();
		if (is_array($posts['data']) && sizeof($posts['data']) > 0) {
			try {
				$em = $this->getEntityManager();
				foreach ($posts['data'] as $item) {
					$feed = new Feed();
					$feed->setNetwork('facebook');
					$feed->setTitle((isset($item['name'])?$item['name']:'Untitled'));
					$feed->setPicture($item['picture']);
					$feed->setContents($item['description']);
					$feed->setLink($item['permalink_url']);
					$feed->setCreatedAt($item['created_time']);
					$em->persist($feed);
					$user->addFeed($feed);
					$em->persist($user);
				}
				$em->flush();
			} catch(\Exception $e) {
				var_dump($e->getMessage());
			}
		} else {
			var_dump('HAVE NOT POSTS');
		}
	}
	
	protected function loadGoogle($user, $id, $token)
	{
		/*$client = new GuzzleHttp\Client();
		$response = $client->request('GET', 'https://www.googleapis.com/plus/v1/people/'.$id.'/openIdConnect', [
			'headers' => array('Authorization' => 'Bearer ' . $token),
			'query' => 'TEST'
		]);
		$posts = json_decode($response->getBody(), true);
		$result = [];
		if (is_array($posts) && sizeof($posts) > 0) {
			try {
				$em = $this->getEntityManager();
				foreach ($posts as $item) {
					$feed = new Feed();
					$feed->setNetwork('facebook');
					$feed->setTitle($item['name']);
					$feed->setPicture($item['picture']);
					$feed->setContents($item['description']);
					$feed->setLink($item['permalink_url']);
					$feed->setCreatedAt($item['created_time']);
					$em->persist($feed);
					$user->addFeed($feed);
					$em->persist($user);
				}
				$em->flush();
			} catch(\Exception $e) {
				var_dump($e->getMessage());
			}
		} else {
			var_dump('HAVE NOT POSTS');
		}*/
	}
	
	protected function loadTwitter($user, $id, $token)
	{
	
	}
	
	protected function loadInstagram($user, $id, $token)
	{
	
	}
}