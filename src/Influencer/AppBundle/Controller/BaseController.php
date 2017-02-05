<?php
namespace Influencer\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Firebase\JWT\JWT;

class BaseController extends Controller
{
	protected function createToken($user)
	{
		$payload = [
			'sub' => is_array($user)?$user[0]->getId():$user->getId(),
			'iat' => time(),
			'exp' => time() + (2 * 7 * 24 * 60 * 60)
		];
		return JWT::encode($payload, 'auth-token-secret');
	}
	
	protected function getUserData()
	{
		$user = $this->getUser();
		return [
			'username' => $user->getUsername(),
			'email' => $user->getEmail(),
			'profileImage' => $user->getProfileImage(),
			'firstName' => $user->getFirstName(),
			'lastName' => $user->getLastName(),
			'facebook' => $user->getFacebook(),
			'google' => $user->getGoogle(),
			'instagram' => $user->getInstagram(),
			'twitter' => $user->getTwitter(),
		];
	}
}
