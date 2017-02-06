<?php 
namespace Influencer\AppBundle\Service;

use GuzzleHttp;
use GuzzleHttp\Subscriber\Oauth\Oauth1;

class FeedLoader
{
	protected $container;
	
	public function __construct($container)
	{
		$this->container = $container;
	}
	
	public function loadFacebookFeed($token, $userId)
	{
		$client = new GuzzleHttp\Client();
		$fields = 'name,caption,created_time,description,picture,permalink_url';
		$response = $client->request('GET', 'https://graph.facebook.com/v2.8/me/feed', [
			'query' => [
				'access_token' => $token,
				'fields' => $fields,
			],
		]);
		return json_decode($response->getBody(), true);
	}
	
	public function loadGoogleFeed($token, $userId)
	{
		$client = new GuzzleHttp\Client();
		$response = $client->request('GET', 'https://www.googleapis.com/plus/v1/activities', [
			'headers' => ['Authorization' => 'Bearer ' . $token],
			'query' => [
				'query' => 'TEST',
				'key' => $this->container->getParameter('google_id')
			]
		]);
		return json_decode($response->getBody(), true);
	}
	
	public function loadTwitterFeed($token, $userId)
	{
		
	}
	
	public function loadInstagramFeed($token, $userId)
	{
		$client = new GuzzleHttp\Client();
		$data = $client->request('GET', 'https://api.instagram.com/v1/users/self/media/recent/', [
			'query' => [
				'access_token' => $token
			]
		]);
		return json_decode($data->getBody(), true);
	}
}