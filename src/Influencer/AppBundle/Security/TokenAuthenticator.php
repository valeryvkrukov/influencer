<?php 
namespace Influencer\AppBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

use Firebase\JWT\JWT;

class TokenAuthenticator extends AbstractGuardAuthenticator
{
	protected $em;
	
	public function __construct($em)
	{
		$this->em = $em;
	}
	
	public function getCredentials(Request $request)
	{
		if (!$request->headers->has('Authorization')) {
			return;
		}
		$token = explode(' ', $request->headers->get('Authorization'))[1];
		$payload = (array) JWT::decode($token, 'auth-token-secret', array('HS256'));
		return $payload;
	}
	
	public function getUser($credentials, UserProviderInterface $userProvider)
	{
		$user = $this->em->getRepository('InfluencerAppBundle:User')->find($credentials['sub']);
		
		return $user;
	}
	
	public function checkCredentials($credentials, UserInterface $user)
	{
		return true;
	}
	
	public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
	{
		return null;
	}
	
	public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
	{
		$data = ['message' => strtr($exception->getMessageKey(), $exception->getMessageData())];
		
		return new JsonResponse($data, Response::HTTP_FORBIDDEN);
	}
	
	public function start(Request $request, AuthenticationException $authException = null)
	{
		$data = ['message' => 'Authentication Required'];
		
		return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
	}
	
	public function supportsRememberMe()
	{
		return false;
	}
}