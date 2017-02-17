<?php
namespace Influencer\AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use GuzzleHttp;
use GuzzleHttp\Subscriber\Oauth\Oauth1;

use Influencer\AppBundle\Controller\BaseController;
use Influencer\AppBundle\Entity\User;

/**
 * @Route("/register")
 */
class RegisterController extends BaseController
{
	/**
	 * @Route("/save", name="inf_registration_save", options={"expose"=true})
	 */
	public function saveAction(Request $request)
	{
		$input = json_decode($request->getContent());
		if (isset($input->email)) {
			$em = $this->getDoctrine()->getManager();
			if ($em->getRepository('InfluencerAppBundle:User')->checkForUniqueUser($input->email)) {
				$user = $em->getRepository('InfluencerAppBundle:User')->createInfluencerUser($input, $this->get('app.feed_loader'));
				return new JsonResponse(['token' => $this->createToken($user)]);
			} else {
				return new JsonResponse(['error' => 'User with given username or/and password already registered'], 409);
			}
		} else {
			return new JsonResponse(['error' => 'Username or/and password is not set'], 403);
		}
	}
	
	/**
	 * @Route("/facebook", name="inf_facebook_link", options={"expose"=true})
	 */
	public function facebookAction(Request $request)
	{
		$input = json_decode($request->getContent());
		$client = new GuzzleHttp\Client();
		$params = [
			'code' => $input->code,
			'client_id' => $input->clientId,
			'redirect_uri' => $input->redirectUri,
			'client_secret' => $this->getParameter('facebook_secret')
		];
		$accessTokenResponse = $client->request('GET', 'https://graph.facebook.com/v2.8/oauth/access_token', ['query' => $params]);
		$accessToken = json_decode($accessTokenResponse->getBody(), true);
		$fields = 'id,email,first_name,last_name,link,name,picture';
		$profileResponse = $client->request('GET', 'https://graph.facebook.com/v2.8/me', [
			'query' => [
				'access_token' => $accessToken['access_token'],
				'fields' => $fields,
			],
		]);
		$profile = json_decode($profileResponse->getBody(), true);
		
		return new JsonResponse([
			'profile' => $profile,
			'token' => $accessToken['access_token'],
		]);
	}
	
	/**
	 * @Route("/google", name="inf_google_link", options={"expose"=true})
	 */
	public function googleAction(Request $request)
	{
		$input = json_decode($request->getContent());
		$client = new GuzzleHttp\Client();
		$params = [
			'code' => $input->code,
			'client_id' => $input->clientId,
			'client_secret' => $this->getParameter('google_secret'),
			'grant_type' => 'authorization_code',
			'redirect_uri' => $input->redirectUri,
		];
		$accessTokenResponse = $client->request('POST', 'https://accounts.google.com/o/oauth2/token', [
			'form_params' => $params
		]);
		$accessToken = json_decode($accessTokenResponse->getBody(), true);
		$profileResponse = $client->request('GET', 'https://www.googleapis.com/plus/v1/people/me/openIdConnect', [
			'headers' => array('Authorization' => 'Bearer ' . $accessToken['access_token'])
		]);
		$profile = json_decode($profileResponse->getBody(), true);
		
		return new JsonResponse([
			'profile' => $profile,
			'token' => $accessToken['access_token'],
		]);
	}
	
	/**
	 * @Route("/twitter", name="inf_twitter_link", options={"expose"=true})
	 */
	public function twitterAction(Request $request)
	{
		$input = json_decode($request->getContent());
		$stack = GuzzleHttp\HandlerStack::create();
		if (!isset($input->oauth_token) || !isset($input->oauth_verifier)) {
			$stack = GuzzleHttp\HandlerStack::create();
			$requestTokenOauth = new Oauth1([
				'consumer_key' => $this->getParameter('twitter_consumer_key'),
				'consumer_secret' => $this->getParameter('twitter_consumer_secret'),
				'callback' => $input->redirectUri,
				'token' => '',
				'token_secret' => ''
			]);
			$stack->push($requestTokenOauth);
			$client = new GuzzleHttp\Client(['handler' => $stack]);
			$requestTokenResponse = $client->request('POST', 'https://api.twitter.com/oauth/request_token', ['auth' => 'oauth']);
			$oauthToken = array();
			parse_str($requestTokenResponse->getBody(), $oauthToken);
			return new JsonResponse($oauthToken);
		} else {
			$accessTokenOauth = new Oauth1([
				'consumer_key' => $this->getParameter('twitter_consumer_key'),
				'consumer_secret' => $this->getParameter('twitter_consumer_secret'),
				'token' => $input->oauth_token,
				'verifier' => $input->oauth_verifier,
				'token_secret' => ''
			]);
			$stack->push($accessTokenOauth);
			$client = new GuzzleHttp\Client(['handler' => $stack]);
			$accessTokenResponse = $client->request('POST', 'https://api.twitter.com/oauth/access_token', ['auth' => 'oauth']);
			$accessToken = array();
			parse_str($accessTokenResponse->getBody(), $accessToken);
			$profileOauth = new Oauth1([
				'consumer_key' => $this->getParameter('twitter_consumer_key'),
				'consumer_secret' => $this->getParameter('twitter_consumer_secret'),
				'oauth_token' => $accessToken['oauth_token'],
				'token_secret' => ''
			]);
			$stack->push($profileOauth);
			$client = new GuzzleHttp\Client(['handler' => $stack]);
			$profileResponse = $client->request('GET', 'https://api.twitter.com/1.1/users/show.json?screen_name='.$accessToken['screen_name'], ['auth' => 'oauth']);
			$profile = json_decode($profileResponse->getBody(), true);
			
			return new JsonResponse([
				'profile' => $profile,
				'token' => $accessToken['oauth_token'],
			]);
		}
	}
	
	/**
	 * @Route("/instagram", name="inf_instagram_link", options={"expose"=true})
	 */
	public function instagramAction(Request $request)
	{
		$input = json_decode($request->getContent());
		$client = new GuzzleHttp\Client();
		$params = [
			'code' => $input->code,
			'client_id' => $input->clientId,
			'client_secret' => $this->getParameter('instagram_secret'),
			'redirect_uri' => $input->redirectUri,
			'grant_type' => 'authorization_code',
		];
		$accessTokenResponse = $client->request('POST', 'https://api.instagram.com/oauth/access_token', ['form_params' => $params]);
		$accessToken = json_decode($accessTokenResponse->getBody(), true);
		
		return new JsonResponse([
			'profile' => $accessToken['user'],
			'token' => $accessToken['access_token'],
		]);
	}
}
