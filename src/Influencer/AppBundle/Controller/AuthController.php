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
 * @Route("/auth")
 */
class AuthController extends BaseController
{
	/**
	 * 
	 */
	public function linkAction(Request $request, $provider)
	{
		
	}
	
	/**
	 * @Route("/login", name="inf_default_auth", options={"expose"=true})
	 */
	public function loginAction(Request $request)
	{
		$input = json_decode($request->getContent());
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('InfluencerAppBundle:User')->findByUsername($input->email);
		$factory = $this->get('security.encoder_factory');
		$encoder = $factory->getEncoder($user[0]);
		
		if (!isset($user[0])) {
			return new JsonResponse(['message' => 'Wrong email and/or password'], 401);
		} else {
			if ($encoder->isPasswordValid($user[0]->getPassword(), $input->password, $user[0]->getSalt())) {
				return new JsonResponse(['token' => $this->createToken($user[0])]);
			} else {
				return new JsonResponse(['message' => 'Wrong email and/or password'], 401);
			}
		}
	}
	
	/**
	 * @Route("/facebook", name="inf_facebook_auth", options={"expose"=true})
	 */
	public function facebookAction(Request $request)
	{
		$input = json_decode($request->getContent());
		$accessToken = $this->get('app.social_connector')->getFacebookToken($input);
		
		$client = new GuzzleHttp\Client();
		/*$params = [
			'code' => $input->code,
			'client_id' => $input->clientId,
			'redirect_uri' => $input->redirectUri,
			'client_secret' => $this->getParameter('facebook_secret')
		];
		$accessTokenResponse = $client->request('GET', 'https://graph.facebook.com/v2.8/oauth/access_token', ['query' => $params]);
		$accessToken = json_decode($accessTokenResponse->getBody(), true);*/
		
		$fields = 'id,email,first_name,last_name,link,name,picture';
		$profileResponse = $client->request('GET', 'https://graph.facebook.com/v2.8/me', [
			'query' => [
				'access_token' => $accessToken['access_token'],
				'fields' => $fields,
			],
		]);
		$profile = json_decode($profileResponse->getBody(), true);
		if (isset($input->link_account) && $input->link_account == 1) {
			return new JsonResponse([
				'profile' => $profile,
				'token' => $accessToken['access_token'],
			]);
		} else {
			$em = $this->getDoctrine()->getManager();
			if ($request->headers->has('Authorization')) {
				$user = $em->getRepository('InfluencerAppBundle:User')->findByOrCondition([
					'facebook' => $profile['id'],
					//'email' => $profile['email'],
					//'username' => $profile['id'],
				]);
				if ($user) {
					return new JsonResponse(['message' => 'There is already a Facebook account that belongs to you'], 409);
				}
				$token = explode(' ', $request->headers->get('Authorization'))[1];
				$payload = (array) JWT::decode($token, 'auth-token-secret', array('HS256'));
				$user = $em->getRepository('InfluencerAppBundle:User')->find($payload['sub']);
				$user->setEnabled(1);
				$user->setFacebook($profile['id']);
				if (!$user->getProfileImage()) {
					$user->setProfileImage($profile['picture']['data']['url']);
				}
				if (!$user->getEmail()) {
					$user->setEmail($profile['email']);
				}
				$user->setFirstName($profile['first_name']);
				$user->setLastName($profile['last_name']);
				$em->persist($user);
				$em->flush();
				return new JsonResponse(['token' => $this->createToken($user)]);
			} else {
				$user = $em->getRepository('InfluencerAppBundle:User')->findByOrCondition([
					'facebook' => $profile['id'],
					//'email' => $profile['email'],
					//'username' => $profile['id'],
				]);
				if ($user) {
					return new JsonResponse(['token' => $this->createToken($user)]);
				}
				$user = new User();
				$user->setEnabled(1);
				$user->setUsername($profile['id']);
				$user->setEmail($profile['email']);
				$user->setFacebook($profile['id']);
				$user->setProfileImage($profile['picture']['data']['url']);
				$user->setFirstName($profile['first_name']);
				$user->setLastName($profile['last_name']);
				if (!$user->getPassword()) {
					$user->setPassword('none');
				}
				$user->addRole('ROLE_CLIENT');
				$em->persist($user);
				$em->flush();
				return new JsonResponse(['token' => $this->createToken($user)]);
			}
		}
	}
	
	/**
	 * @Route("/google", name="inf_google_auth", options={"expose"=true})
	 */
	public function googleAction(Request $request)
	{
		$input = json_decode($request->getContent());
		$accessToken = $this->get('app.social_connector')->getGoogleToken($input);
		
		$client = new GuzzleHttp\Client();
		/*$params = [
			'code' => $input->code,
			'client_id' => $input->clientId,
			'client_secret' => $this->getParameter('google_secret'),
			'grant_type' => 'authorization_code',
			'redirect_uri' => $input->redirectUri,
		];
		$accessTokenResponse = $client->request('POST', 'https://accounts.google.com/o/oauth2/token', [
			'form_params' => $params
		]);
		$accessToken = json_decode($accessTokenResponse->getBody(), true);*/
		
		$profileResponse = $client->request('GET', 'https://www.googleapis.com/plus/v1/people/me/openIdConnect', [
			'headers' => array('Authorization' => 'Bearer ' . $accessToken['access_token'])
		]);
		$profile = json_decode($profileResponse->getBody(), true);
		if (isset($input->link_account) && $input->link_account == 1) {
			return new JsonResponse([
				'profile' => $profile,
				'token' => $accessToken['access_token'],
			]);
		} else {
			$em = $this->getDoctrine()->getManager();
			if ($request->headers->has('Authorization')) {
				$user = $em->getRepository('InfluencerAppBundle:User')->findByOrCondition([
					'google' => $profile['id'],
					//'email' => $profile['email'],
					//'username' => $profile['id'],
				]);
				if ($user) {
					return new JsonResponse(['message' => 'There is already a Google account that belongs to you'], 409);
				}
				$token = explode(' ', $request->headers->get('Authorization'))[1];
				$payload = (array) JWT::decode($token, 'auth-token-secret', array('HS256'));
				$user = $em->getRepository('InfluencerAppBundle:User')->find($payload['sub']);
				$user->setGoogle($profile['id']);
				$user->setEnabled(1);
				if (!$user->getEmail()) {
					$user->setEmail($profile['email']);
				}
				$user->setFirstName($profile['given_name']);
				$user->setLastName($profile['family_name']);
				$em->persist($user);
				$em->flush();
				return new JsonResponse(['token' => $this->createToken($user)]);
			} else {
				$user = $em->getRepository('InfluencerAppBundle:User')->findByOrCondition([
					'google' => $profile['sub'],
					//'email' => $profile['email'],
					//'username' => $profile['sub'],
				]);
				if ($user) {
					return new JsonResponse(['token' => $this->createToken($user)]);
				}
				$user = new User();
				$user->setEnabled(1);
				$user->setUsername($profile['sub']);
				$user->setEmail($profile['email']);
				$user->setGoogle($profile['sub']);
				$user->setFirstName($profile['given_name']);
				$user->setLastName($profile['family_name']);
				if (!$user->getPassword()) {
					$user->setPassword('none');
				}
				$user->addRole('ROLE_CLIENT');
				$em->persist($user);
				$em->flush();
				return new JsonResponse(['token' => $this->createToken($user)]);
			}
		}
	}
	
	/**
	 * @Route("/twitter", name="inf_twitter_auth", options={"expose"=true})
	 */
	public function twitterAction(Request $request)
	{
		$input = json_decode($request->getContent());
		
		$stack = GuzzleHttp\HandlerStack::create();
		if (!isset($input->oauth_token) || !isset($input->oauth_verifier)) {
			$oauthToken = $this->get('app.social_connector')->getTwitterToken1($input);
			/*$stack = GuzzleHttp\HandlerStack::create();
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
			parse_str($requestTokenResponse->getBody(), $oauthToken);*/
			return new JsonResponse($oauthToken);
		} else {
			/*$accessTokenOauth = new Oauth1([
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
			parse_str($accessTokenResponse->getBody(), $accessToken);*/
			$accessToken = $this->get('app.social_connector')->getTwitterToken2($input);
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
			if (isset($input->link_account) && $input->link_account == 1) {
				return new JsonResponse([
					'profile' => $profile,
					'token' => $accessToken['oauth_token'],
				]);
			} else {
				$em = $this->getDoctrine()->getManager();
				if ($request->headers->has('Authorization')) {
					$user = $em->getRepository('InfluencerAppBundle:User')->findByOrCondition([
						'twitter' => $profile['id'],
						//'username' => $profile['id'],
					]);
					if ($user) {
						return new JsonResponse(['message' => 'There is already a Twitter account that belongs to you'], 409);
					}
					$token = explode(' ', $request->header('Authorization'))[1];
					$payload = (array) JWT::decode($token, 'auth-token-secret', array('HS256'));
					$user = $em->getRepository('InfluencerAppBundle:User')->findByOrCondition([
						'twitter' => $payload['sub'],
						//'username' => $profile['id'],
					]);
					$user->setEnabled(1);
					$user->setTwitter($profile['id']);
					if (!$user->getFirstName()) {
						$splittedName = explode(' ', $profile['name']);
						$firstName = $splittedName[0];
						$user->setFirstName($firstName);
						$lastName = array_pop($splittedName);
						if ($lastName != $firstName) {
							$user->setLastName($lastName);
						}
					}
					$em->persist($user);
					$em->flush();
					return new JsonResponse(['token' => $this->createToken($user)]);
				} else {
					$user = $em->getRepository('InfluencerAppBundle:User')->findByOrCondition([
						'twitter' => $profile['id'],
						'username' => $profile['id'],
					]);
					if ($user) {
						return new JsonResponse(['token' => $this->createToken($user)]);
					}
					$user = new User();
					$user->setEnabled(1);
					$user->setUsername($profile['id']);
					$user->setEmail('none-@'.time());
					$user->setTwitter($profile['id']);
					$splittedName = explode(' ', $profile['name']);
					$firstName = $splittedName[0];
					$user->setFirstName($firstName);
					$lastName = array_pop($splittedName);
					if ($lastName != $firstName) {
						$user->setLastName($lastName);
					}
					if (!$user->getPassword()) {
						$user->setPassword('none');
					}
					$user->addRole('ROLE_CLIENT');
					$em->persist($user);
					$em->flush();
					return new JsonResponse(['token' => $this->createToken($user)]);
				}
			}
		}
	}
	
	/**
	 * @Route("/instagram", name="inf_instagram_auth", options={"expose"=true})
	 */
	public function instagramAction(Request $request)
	{
		$input = json_decode($request->getContent());
		/*$client = new GuzzleHttp\Client();
		$params = [
			'code' => $input->code,
			'client_id' => $input->clientId,
			'client_secret' => $this->getParameter('instagram_secret'),
			'redirect_uri' => $input->redirectUri,
			'grant_type' => 'authorization_code',
		];
		$accessTokenResponse = $client->request('POST', 'https://api.instagram.com/oauth/access_token', ['form_params' => $params]);
		$accessToken = json_decode($accessTokenResponse->getBody(), true);*/
		
		$accessToken = $this->get('app.social_connector')->getInstagramToken($input);
		
		if (isset($input->link_account) && $input->link_account == 1) {
			return new JsonResponse([
				'profile' => $accessToken['user'],
				'token' => $accessToken['access_token'],
			]);
		} else {
			$em = $this->getDoctrine()->getManager();
			if ($request->headers->has('Authorization')) {
				$user = $em->getRepository('InfluencerAppBundle:User')->findBy(['instagram' => $accessToken['user']['id']]);
				if ($user) {
					return new JsonResponse(['message' => 'There is already a Instagram account that belongs to you'], 409);
				}
				$token = explode(' ', $request->header('Authorization'))[1];
				$payload = (array) JWT::decode($token, 'auth-token-secret', array('HS256'));
				$user = $em->getRepository('InfluencerAppBundle:User')->find($payload['sub']);
				$user->setUsername($accessToken['user']['id']);
				$user->setEnabled(1);
				$user->setInstagram($accessToken['user']['id']);
				if (!$user->getFirstName()) {
					$splittedName = explode(' ', $accessToken['user']['full_name']);
					$firstName = $splittedName[0];
					$user->setFirstName($firstName);
					$lastName = array_pop($splittedName);
					if ($lastName != $firstName) {
						$user->setLastName($lastName);
					}
				}
				$em->persist($user);
				$em->flush();
				return new JsonResponse(['token' => $this->createToken($user)]);
			} else {
				$user = $em->getRepository('InfluencerAppBundle:User')->findBy(['instagram' => $accessToken['user']['id']]);
				if ($user) {
					return new JsonResponse(['token' => $this->createToken($user)]);
				}
				$user = new User();
				$user->setEnabled(1);
				$user->setUsername($accessToken['user']['id']);
				$user->setEmail('none-@'.time());
				$user->setInstagram($accessToken['user']['id']);
				$splittedName = explode(' ', $accessToken['user']['full_name']);
				$firstName = $splittedName[0];
				$user->setFirstName($firstName);
				$lastName = array_pop($splittedName);
				if ($lastName != $firstName) {
					$user->setLastName($lastName);
				}
				if (!$user->getPassword()) {
					$user->setPassword('none');
				}
				$user->addRole('ROLE_CLIENT');
				$em->persist($user);
				$em->flush();
				return new JsonResponse(['token' => $this->createToken($user)]);
			}
		}
	}
}
