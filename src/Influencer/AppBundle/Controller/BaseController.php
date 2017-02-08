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
			'id' => $user->getId(),
			'username' => $user->getUsername(),
			'email' => $user->getEmail(),
			'brief' => $user->getBrief(),
			'profileImage' => $user->getProfileImage(),
			'profileCover' => $user->getProfileCover(),
			'firstName' => $user->getFirstName(),
			'lastName' => $user->getLastName(),
			'age' => $user->getAge(),
			'contactNumber' => $user->getContactNumber(),
			'secondaryNumber' => $user->getSecondaryNumber(),
			'website' => $user->getWebsite(),
			'languages' => (array)$user->getLanguages(),
			'countries' => (array)$user->getCountries(),
			'facebook' => $user->getFacebook(),
			'google' => $user->getGoogle(),
			'instagram' => $user->getInstagram(),
			'twitter' => $user->getTwitter(),
			'role' => $this->getReadableRole($user),
		];
	}
	
	protected function getReadableRole($user)
	{
		$roles = $user->getRoles();
		if (in_array('ROLE_ADMIN', $roles)) {
			return 'admin';
		}
		if (in_array('ROLE_INFLUENCER', $roles)) {
			return 'influencer';
		}
		if (in_array('ROLE_CLIENT', $roles)) {
			return 'client';
		}
	}
}
