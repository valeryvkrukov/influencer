<?php 
namespace Influencer\AppBundle\Service;

use GuzzleHttp;
use GuzzleHttp\Subscriber\Oauth\Oauth1;

class SocialConnect
{
	protected $container;
	
	public function __construct($container)
	{
		$this->container = $container;
	}
	
	public function getFacebookToken($input)
	{
		$client = new GuzzleHttp\Client();
		$params = [
			'code' => $input->code,
			'client_id' => $input->clientId,
			'redirect_uri' => $input->redirectUri,
			'client_secret' => $this->container->getParameter('facebook_secret')
		];
		$accessTokenResponse = $client->request('GET', 'https://graph.facebook.com/v2.8/oauth/access_token', ['query' => $params]);
		return json_decode($accessTokenResponse->getBody(), true);
	}
	
	public function getGoogleToken($input)
	{
		$client = new GuzzleHttp\Client();
		$params = [
			'code' => $input->code,
			'client_id' => $input->clientId,
			'client_secret' => $this->container->getParameter('google_secret'),
			'grant_type' => 'authorization_code',
			'redirect_uri' => $input->redirectUri,
		];
		$accessTokenResponse = $client->request('POST', 'https://accounts.google.com/o/oauth2/token', [
			'form_params' => $params
		]);
		return json_decode($accessTokenResponse->getBody(), true);
	}
	
	public function getTwitterToken1($input)
	{
		$stack = GuzzleHttp\HandlerStack::create();
		$requestTokenOauth = new Oauth1([
			'consumer_key' => $this->container->getParameter('twitter_consumer_key'),
			'consumer_secret' => $this->container->getParameter('twitter_consumer_secret'),
			'callback' => $input->redirectUri,
			'token' => '',
			'token_secret' => ''
		]);
		$stack->push($requestTokenOauth);
		$client = new GuzzleHttp\Client(['handler' => $stack]);
		$requestTokenResponse = $client->request('POST', 'https://api.twitter.com/oauth/request_token', ['auth' => 'oauth']);
		$oauthToken = array();
		parse_str($requestTokenResponse->getBody(), $oauthToken);
		return $oauthToken;
	}
	
	public function getTwitterToken2($input)
	{
		$stack = GuzzleHttp\HandlerStack::create();
		$accessTokenOauth = new Oauth1([
			'consumer_key' => $this->container->getParameter('twitter_consumer_key'),
			'consumer_secret' => $this->container->getParameter('twitter_consumer_secret'),
			'token' => $input->oauth_token,
			'verifier' => $input->oauth_verifier,
			'token_secret' => ''
		]);
		$stack->push($accessTokenOauth);
		$client = new GuzzleHttp\Client(['handler' => $stack]);
		$accessTokenResponse = $client->request('POST', 'https://api.twitter.com/oauth/access_token', ['auth' => 'oauth']);
		$accessToken = array();
		parse_str($accessTokenResponse->getBody(), $accessToken);
		return $accessToken;
	}
	
	public function getInstagramToken($input)
	{
		$client = new GuzzleHttp\Client();
		$params = [
			'code' => $input->code,
			'client_id' => $input->clientId,
			'client_secret' => $this->container->getParameter('instagram_secret'),
			'redirect_uri' => $input->redirectUri,
			'grant_type' => 'authorization_code',
		];
		$accessTokenResponse = $client->request('POST', 'https://api.instagram.com/oauth/access_token', ['form_params' => $params]);
		return json_decode($accessTokenResponse->getBody(), true);
	}
}