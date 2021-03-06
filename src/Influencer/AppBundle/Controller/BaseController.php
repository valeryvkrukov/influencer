<?php
namespace Influencer\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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
		$serializer = $this->get('serializer');
		$langItems = json_decode($serializer->serialize($user->getLanguages(), 'json'));
		$languages = [];
		foreach ($langItems as $item) {
			$languages[] = [
				'code' => $item->code,
				'lang' => $item->name,
			];
		}
		$countryItems = json_decode($serializer->serialize($user->getCountries(), 'json'));
		$countries = [];
		foreach ($countryItems as $item) {
			$countries[] = [
				'code' => $item->code,
				'country' => $item->name,
			];
		}
		$audienceItems = json_decode($serializer->serialize($user->getAudience(), 'json'));
		$audience = [];
		foreach ($audienceItems as $item) {
			$audience[] = [
				'tag' => $item->code,
				'name' => $item->name,
				'icon' => isset($item->icon)?$item->icon:null,
			];
		}
		$priceItems = json_decode($serializer->serialize($user->getPrices(), 'json'));
		$prices = [];
		foreach ($priceItems as $item) {
			$prices[] = [
				'tag' => $item->tag,
				'cost' => floatval($item->cost),
				'name' => $item->name,
				'icon' => isset($item->icon)?$item->icon:null,
			];
		}
		$age = $user->getAge();
		switch ($age) {
			case ($age > 17 && $age < 25):
				$bracket = '18-24';
				break;
			case ($age > 24 && $age < 30):
				$bracket = '25-29';
				break;
			case ($age > 29 && $age < 35):
				$bracket = '30-34';
				break;
			case ($age > 34 && $age < 40):
				$bracket = '35-39';
				break;
			case ($age > 39 && $age < 45):
				$bracket = '40-44';
				break;
			case ($age > 44):
				$bracket = '45+';
				break;
			default:
				$bracket = 0;
		}
		return [
			'id' => $user->getId(),
			'username' => $user->getUsername(),
			'email' => $user->getEmail(),
			'brief' => $user->getBrief(),
			'profileImage' => $user->getProfileImage(),
			'profileCover' => $user->getProfileCover(),
			'firstName' => $user->getFirstName(),
			'lastName' => $user->getLastName(),
			'age' => $age,
			'ageBracket' => $bracket,
			'gender' => $user->getGender(),
			'contactNumber' => $user->getContactNumber(),
			'secondaryNumber' => $user->getSecondaryNumber(),
			'website' => $user->getWebsite(),
			'languages' => $languages,
			'countries' => $countries,
			'audience' => $audience,
			'prices' => $prices,
			'facebook' => json_decode($serializer->serialize($user->getFacebook(), 'json')),
			'google' => json_decode($serializer->serialize($user->getGoogle(), 'json')),
			'instagram' => json_decode($serializer->serialize($user->getInstagram(), 'json')),
			'twitter' => json_decode($serializer->serialize($user->getTwitter(), 'json')),
			'klout' => $user->getKlout(),
			'kloutScore' => $this->get('app.influencer_data')->getKloutScore($user->getKlout()),
			'role' => $this->getReadableRole($user),
			//'twitterFollowers' => $this->get('app.influencer_data')->getTwitterStats($user->getId()),
			//'instagramFans' => $this->get('app.influencer_data')->getInstagramStats($user->getId()),
			//'youtubeViews' => 0,//$this->get('app.influencer_data')->getGoogleStats($user->getId()),
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
	
	protected function sendConfirmationMessage($user, $role)
	{
		try {
			$link = $this->get('router')->generate('inf_email_confirmation', ['token' => $user->getConfirmationToken()], UrlGeneratorInterface::ABSOLUTE_URL);
			$message = \Swift_Message::newInstance()
				->setSubject('Confirmation message')
				->setFrom($this->getParameter('email_from'))
				->setTo($user->getEmail())
				->setBody($this->renderView('Emails/registration-'.$role.'.html.twig', [
					'name' => implode(' ', [$user->getFirstName(), $user->getLastName()]),
					'link' => $link,
				]), 'text/html');
			$this->get('mailer')->send($message);
			return true;
		} catch(\Exception $e) {
			return false;
		}
	}
	
	protected function sendResetPasswordMessage($user, $password)
	{
		try {
			$message = \Swift_Message::newInstance()
				->setSubject('New password')
				->setFrom($this->getParameter('email_from'))
				->setTo($user->getEmail())
				->setBody($this->renderView('Emails/password-reset.html.twig', [
					'name' => implode(' ', [$user->getFirstName(), $user->getLastName()]),
					'password' => $password,
				]), 'text/html');
			$this->get('mailer')->send($message);
			return true;
		} catch(\Exception $e) {
			var_dump($e->getMessage());die();
			return false;
		}
	}
}
