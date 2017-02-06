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
		$fields = 'id,email,first_name,last_name,link,name,picture';
		$response = $client->request('GET', 'https://graph.facebook.com/v2.8/me', [
			'query' => [
				'access_token' => $token,
				'fields' => $fields,
			],
		]);
		return json_decode($response->getBody(), true);
	}
	
	public function loadGoogleFeed($token, $userId)
	{
		
	}
	
	public function loadTwitterFeed($token, $userId)
	{
		
	}
	
	public function loadInstagramFeed($token, $userId)
	{
		$client = new GuzzleHttp\Client();
		$data = $client->request('GET', 'https://api.instagram.com/v1/users/search', [
			'query' => [
				'q' => 'kyliejenner',
				//'lat' => 48.858844,
				//'lng' => 2.294351,
				'access_token' => $token
			]
		]);
		var_dump($data->getBody());die();
		return json_decode($data->getBody(), true);
	}
}