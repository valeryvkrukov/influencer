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
		$response = $client->request('GET', 'https://graph.facebook.com/v2.8/me', [
			'query' => [
				'access_token' => $token,
				'fields' => 'posts.limit(10){message,picture,caption,link,created_time,likes{name},place}',
			],
		]);
		return json_decode($response->getBody(), true);
	}
	
	public function loadGoogleFeed($token, $userId)
	{
		$client = new GuzzleHttp\Client();
		$response = $client->request('GET', 'https://www.googleapis.com/youtube/v3/activities', [
			'headers' => ['Authorization' => 'Bearer ' . $token],
			'query' => [
				'part' => 'snippet',
				'home' => 'true',
				'key' => $this->container->getParameter('google_id')
			]
		]);
		return json_decode($response->getBody(), true);
	}
	
	public function loadTwitterFeed($token, $network)
	{
		$stack = GuzzleHttp\HandlerStack::create();
		$profileOauth = new Oauth1([
			'consumer_key' => $this->container->getParameter('twitter_consumer_key'),
			'consumer_secret' => $this->container->getParameter('twitter_consumer_secret'),
			'oauth_token' => $token,
			'token_secret' => ''
		]);
		$stack->push($profileOauth);
		$client = new GuzzleHttp\Client(['handler' => $stack]);
		$response = $client->request('GET', 'https://api.twitter.com/1.1/statuses/user_timeline.json?user_id='.$network->getUserId(), ['auth' => 'oauth']);
		return json_decode($response->getBody(), true);
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