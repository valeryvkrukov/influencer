<?php
namespace Influencer\AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use GuzzleHttp;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use Firebase\JWT\JWT;

use Influencer\AppBundle\Controller\BaseController;
use Influencer\AppBundle\Entity\User;
use Influencer\AppBundle\Entity\Network;

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
		
		if (!isset($user[0])) {
			return new JsonResponse(['message' => 'Wrong email and/or password'], 401);
		} else {
			$encoder = $factory->getEncoder($user[0]);
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
		$fields = 'id,email,first_name,last_name,link,name,picture';
		$profileResponse = $client->request('GET', 'https://graph.facebook.com/v2.8/me', [
			'query' => [
				'access_token' => $accessToken['access_token'],
				'fields' => $fields,
			],
		]);
		$profile = json_decode($profileResponse->getBody(), true);
		if (isset($input->link_account) && $input->link_account == 1) {
			if (isset($input->link_to_user) && $input->link_to_user) {
				$em = $this->getDoctrine()->getManager();
				$user = $em->getRepository('InfluencerAppBundle:User')->find($input->link_to_user);
				if ($user) {
					$_network = new Network();
					$_network->setCode('facebook');
					$_network->setName('Facebook');
					$_network->setUserId($profile['id']);
					$_network->setUserName($profile['email']);
					$_network->setToken($accessToken['access_token']);
					$em->persist($_network);
					$user->setFacebook($_network);
					$em->persist($user);
					$em->flush();
				}
			}
			return new JsonResponse([
				'profile' => $profile,
				'token' => $accessToken['access_token'],
			]);
		} else {
			$em = $this->getDoctrine()->getManager();
			if ($request->headers->has('Authorization')) {
				$user = $em->getRepository('InfluencerAppBundle:User')->findUserByNetworkId('facebook', $profile['id']);
				if ($user) {
					return new JsonResponse(['message' => 'There is already a Facebook account that belongs to you'], 409);
				}
				$token = explode(' ', $request->headers->get('Authorization'))[1];
				$payload = (array) JWT::decode($token, 'auth-token-secret', array('HS256'));
				$user = $em->getRepository('InfluencerAppBundle:User')->find($payload['sub']);
				$user->setEnabled(1);
				$_network = new Network();
				$_network->setCode('facebook');
				$_network->setName('Facebook');
				$_network->setUserId($profile['id']);
				$_network->setUserName($profile['email']);
				$_network->setToken($accessToken['access_token']);
				$em->persist($_network);
				$user->setFacebook($_network);
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
				$user = $em->getRepository('InfluencerAppBundle:User')->findUserByNetworkId('facebook', $profile['id']);
				if ($user) {
					return new JsonResponse(['token' => $this->createToken($user)]);
				}
				$user = new User();
				$user->setEnabled(1);
				$user->setUsername($profile['id']);
				$user->setEmail($profile['email']);
				$_network = new Network();
				$_network->setCode('facebook');
				$_network->setName('Facebook');
				$_network->setUserId($profile['id']);
				$_network->setUserName($profile['email']);
				$_network->setToken($accessToken['access_token']);
				$em->persist($_network);
				$user->setFacebook($_network);
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
		$profileResponse = $client->request('GET', 'https://www.googleapis.com/plus/v1/people/me/openIdConnect', [
			'headers' => array('Authorization' => 'Bearer ' . $accessToken['access_token'])
		]);
		$profile = json_decode($profileResponse->getBody(), true);
		if (isset($input->link_account) && $input->link_account == 1) {
			if (isset($input->link_to_user) && $input->link_to_user) {
				$em = $this->getDoctrine()->getManager();
				$user = $em->getRepository('InfluencerAppBundle:User')->find($input->link_to_user);
				if ($user) {
					$_network = new Network();
					$_network->setCode('google');
					$_network->setName('YouTube');
					$_network->setUserId($profile['sub']);
					$_network->setUserName($profile['email']);
					$_network->setToken($accessToken['access_token']);
					$em->persist($_network);
					$user->setGoogle($_network);
					$em->persist($user);
					$em->flush();
				}
			}
			return new JsonResponse([
				'profile' => $profile,
				'token' => $accessToken['access_token'],
			]);
		} else {
			$em = $this->getDoctrine()->getManager();
			if ($request->headers->has('Authorization')) {
				$user = $em->getRepository('InfluencerAppBundle:User')->findUserByNetworkId('google', $profile['sub']);
				if ($user) {
					return new JsonResponse(['message' => 'There is already a Google account that belongs to you'], 409);
				}
				$token = explode(' ', $request->headers->get('Authorization'))[1];
				$payload = (array) JWT::decode($token, 'auth-token-secret', array('HS256'));
				$user = $em->getRepository('InfluencerAppBundle:User')->find($payload['sub']);
				$_network = new Network();
				$_network->setCode('google');
				$_network->setName('YouTube');
				$_network->setUserId($profile['sub']);
				$_network->setUserName($profile['email']);
				$_network->setToken($accessToken['access_token']);
				$em->persist($_network);
				$user->setGoogle($_network);
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
				$user = $em->getRepository('InfluencerAppBundle:User')->findUserByNetworkId('google', $profile['sub']);
				if ($user) {
					return new JsonResponse(['token' => $this->createToken($user)]);
				}
				$user = new User();
				$user->setEnabled(1);
				$user->setUsername($profile['sub']);
				$user->setEmail($profile['email']);
				$_network = new Network();
				$_network->setCode('google');
				$_network->setName('YouTube');
				$_network->setUserId($profile['sub']);
				$_network->setUserName($profile['email']);
				$_network->setToken($accessToken['access_token']);
				$em->persist($_network);
				$user->setGoogle($_network);
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
			return new JsonResponse($oauthToken);
		} else {
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
				if (isset($input->link_to_user) && $input->link_to_user) {
					$em = $this->getDoctrine()->getManager();
					$user = $em->getRepository('InfluencerAppBundle:User')->find($input->link_to_user);
					if ($user) {
						$_network = new Network();
						$_network->setCode('twitter');
						$_network->setName('Twitter');
						$_network->setUserId($profile['id']);
						$_network->setUserName($profile['screen_name']);
						$_network->setToken($accessToken['oauth_token']);
						$em->persist($_network);
						$user->setTwitter($_network);
						$em->persist($user);
						$em->flush();
					}
				}
				return new JsonResponse([
					'profile' => $profile,
					'token' => $accessToken['oauth_token'],
				]);
			} else {
				$em = $this->getDoctrine()->getManager();
				if ($request->headers->has('Authorization')) {
					$user = $em->getRepository('InfluencerAppBundle:User')->findUserByNetworkId('twitter', $profile['id']);
					if ($user) {
						return new JsonResponse(['message' => 'There is already a Twitter account that belongs to you'], 409);
					}
					$token = explode(' ', $request->headers->get('Authorization'))[1];
					$payload = (array) JWT::decode($token, 'auth-token-secret', array('HS256'));
					$user = $em->getRepository('InfluencerAppBundle:User')->find($payload['sub']);
					$user->setEnabled(1);
					$_network = new Network();
					$_network->setCode('twitter');
					$_network->setName('Twitter');
					$_network->setUserId($profile['id']);
					$_network->setUserName($profile['screen_name']);
					$_network->setToken($accessToken['oauth_token']);
					$em->persist($_network);
					$user->setTwitter($_network);
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
					$user = $em->getRepository('InfluencerAppBundle:User')->findUserByNetworkId('twitter', $profile['id']);
					if ($user) {
						return new JsonResponse(['token' => $this->createToken($user)]);
					}
					$user = new User();
					$user->setEnabled(1);
					$user->setUsername($profile['id']);
					$user->setEmail('none-@'.time());
					$_network = new Network();
					$_network->setCode('twitter');
					$_network->setName('Twitter');
					$_network->setUserId($profile['id']);
					$_network->setUserName($profile['screen_name']);
					$_network->setToken($accessToken['oauth_token']);
					$em->persist($_network);
					$user->setTwitter($_network);
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
		$accessToken = $this->get('app.social_connector')->getInstagramToken($input);
		
		if (isset($input->link_account) && $input->link_account == 1) {
			if (isset($input->link_to_user) && $input->link_to_user) {
				$em = $this->getDoctrine()->getManager();
				$user = $em->getRepository('InfluencerAppBundle:User')->find($input->link_to_user);
				if ($user) {
					$_network = new Network();
					$_network->setCode('instagram');
					$_network->setName('Instagram');
					$_network->setUserId($accessToken['user']['id']);
					$_network->setUserName($accessToken['user']['username']);
					$_network->setToken($accessToken['access_token']);
					$em->persist($_network);
					$user->setInstagram($_network);
					$em->persist($user);
					$em->flush();
				}
			}
			return new JsonResponse([
				'profile' => $accessToken['user'],
				'token' => $accessToken['access_token'],
			]);
		} else {
			$em = $this->getDoctrine()->getManager();
			if ($request->headers->has('Authorization')) {
				$user = $em->getRepository('InfluencerAppBundle:User')->findUserByNetworkId('instagram', $accessToken['user']['id']);
				if ($user) {
					return new JsonResponse(['message' => 'There is already a Instagram account that belongs to you'], 409);
				}
				$token = explode(' ', $request->headers->get('Authorization'))[1];
				$payload = (array) JWT::decode($token, 'auth-token-secret', array('HS256'));
				$user = $em->getRepository('InfluencerAppBundle:User')->find($payload['sub']);
				$user->setUsername($accessToken['user']['id']);
				$user->setEnabled(1);
				$_network = new Network();
				$_network->setCode('instagram');
				$_network->setName('Instagram');
				$_network->setUserId($accessToken['user']['id']);
				$_network->setUserName($accessToken['user']['username']);
				$_network->setToken($accessToken['access_token']);
				$em->persist($_network);
				$user->setInstagram($_network);
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
				$user = $em->getRepository('InfluencerAppBundle:User')->findUserByNetworkId('instagram', $accessToken['user']['id']);
				if ($user) {
					return new JsonResponse(['token' => $this->createToken($user)]);
				}
				$user = new User();
				$user->setEnabled(1);
				$user->setUsername($accessToken['user']['id']);
				$user->setEmail('none-@'.time());
				$_network = new Network();
				$_network->setCode('instagram');
				$_network->setName('Instagram');
				$_network->setUserId($accessToken['user']['id']);
				$_network->setUserName($accessToken['user']['username']);
				$_network->setToken($accessToken['access_token']);
				$em->persist($_network);
				$user->setInstagram($_network);
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
