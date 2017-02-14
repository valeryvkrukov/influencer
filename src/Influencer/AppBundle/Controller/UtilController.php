<?php

namespace Influencer\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Intl\Intl;

/**
 * @Route("/util")
 */
class UtilController extends Controller
{
    /**
     * @Route("/get-intl-vars", name="inf_get_intl_vars", options={"expose"=true})
     */
    public function getIntlVarsAction()
    {
    	$languages = [];
    	$countries = [];
    	foreach (Intl::getLanguageBundle()->getLanguageNames() as $code => $lang) {
    		$languages[] = [
    			'code' => $code,
    			'lang' => $lang,
    		];
    	}
    	foreach (Intl::getRegionBundle()->getCountryNames() as $code => $country) {
    		$countries[] = [
    			'code' => $code,
    			'country' => $country,
    		];
    	}
    	return new JsonResponse([
    		'status' => 200,
    		'data' => [
    			'languages' => $languages,
    			'countries' => $countries,
    		],
    	]);
    }

    /**
     * @Route("/get-post-types", name="inf_get_post_types", options={"expose"=true})
     */
    public function getPostTypesAction()
    {
    	$types = [
    		'twitter' => [
    			'name' => 'Event',
    			'tag' => 'event',
    			'icon' => 'pg-calender',
    		],
    		'google' => [
    			'name' => 'Video',
    			'tag' => 'video',
    			'icon' => 'pg-movie',
    		],
    		'instagram' => [
    			'name' => 'Photoshoot',
    			'tag' => 'photo',
    			'icon' => 'pg-camera',
    		],
    		'facebook' => [
    			'name' => 'Social media',
    			'tag' => 'social',
    			'icon' => 'pg-social',
    		]
    	];
    	return new JsonResponse([
    		'status' => 200,
    		'data' => [
    			'types' => $types,
    		],
    	]);
    }
    
    /**
     * @Route("/get-categories", name="inf_get_categories", options={"expose"=true})
     */
    public function getCategories()
    {
    	$categories = [
    		[
    			'name' => 'Art & Design',
    			'tag' => 'art-n-design',
    			'icon' => '',
    		],
    		[
    			'name' => 'Beauty',
    			'tag' => 'beauty',
    			'icon' => '',
    		],
    		[
    			'name' => 'Women’s Fashion & Jewelry',
    			'tag' => 'women-fashion-n-jewelry',
    			'icon' => '',
    		],
    		[
    			'name' => 'Men’s Fashion & Jewelry',
    			'tag' => 'men-fashion-n-jewelry',
    			'icon' => '',
    		],
    		[
    			'name' => 'Travel',
    			'tag' => 'travel',
    			'icon' => '',
    		],
    		[
    			'name' => 'Adventure',
    			'tag' => 'adventure',
    			'icon' => '',
    		],
    		[
    			'name' => 'Auto',
    			'tag' => 'auto',
    			'icon' => '',
    		],
    		[
    			'name' => 'Sports',
    			'tag' => 'sport',
    			'icon' => '',
    		],
    		[
    			'name' => 'Food',
    			'tag' => 'food',
    			'icon' => '',
    		],
    		[
    			'name' => 'Fitness',
    			'tag' => 'fitness',
    			'icon' => '',
    		],
    		[
    			'name' => 'Wellness & Nutrition',
    			'tag' => 'wellness-n-nutrition',
    			'icon' => '',
    		],
    		[
    			'name' => 'Parenting',
    			'tag' => 'parenting',
    			'icon' => '',
    		],
    		[
    			'name' => 'Life Coach',
    			'tag' => 'life-coach',
    			'icon' => '',
    		],
    		[
    			'name' => 'Music',
    			'tag' => 'music',
    			'icon' => '',
    		],
    		[
    			'name' => 'Nightlife',
    			'tag' => 'nightlife',
    			'icon' => '',
    		],
    		[
    			'name' => 'Entertainment',
    			'tag' => 'entertainment',
    			'icon' => '',
    		],
    		[
    			'name' => 'Pets',
    			'tag' => 'pets',
    			'icon' => '',
    		],
    		[
    			'name' => 'Tech',
    			'tag' => 'tech',
    			'icon' => '',
    		],
    	];
    	return new JsonResponse([
    		'status' => 200,
    		'data' => [
    			'categories' => $categories,
    		],
    	]);
    }

    /**
     * @Route("/get-social-networks", name="inf_get_social_networks", options={"expose"=true})
     */
    public function getSocialNetworksAction()
    {
    	$networks = [
    		[
    			'name' => 'Facebook',
    			'tag' => 'facebook',
    		],
    		[
    			'name' => 'Google+',
    			'tag' => 'google',
    		],
    		[
    			'name' => 'Twitter',
    			'tag' => 'twitter',
    		],
    		[
    			'name' => 'Instagram',
    			'tag' => 'instagram',
    		],
    	];
    	return new JsonResponse([
    		'status' => 200,
    		'data' => [
    			'networks' => $networks
    		],
    	]);
    }

    /**
     * @Route("/check-for-username", name="inf_check_for_username", options={"expose"=true})
     */
    public function checkUsernameAction(Request $request)
    {
    	$username = $request->request->get('username');
    	$response = ['status' => 'fail'];
    	if ($username) {
    		$em = $this->getDoctrine()->getManager();
    		$user = $em->createQuery('SELECT u FROM InfluencerAppBundle:User u WHERE u.username = :username')
    			->setParameter('username', $username)
    			->getOneOrNullResult();
    		if ($user === null) {
    			$response['status'] = 'success';
    		}
    	}
    	return new JsonResponse($response);
    }

    /**
     * @Route("/check-for-email", name="inf_check_for_email", options={"expose"=true})
     */
    public function checkEmailAction(Request $request)
    {
    	$email = $request->request->get('email');
    	$response = ['status' => 'fail'];
    	if ($email) {
    		$em = $this->getDoctrine()->getManager();
    		$user = $em->createQuery('SELECT u FROM InfluencerAppBundle:User u WHERE u.email = :email')
    			->setParameter('email', $email)
    			->getOneOrNullResult();
    		if ($user === null) {
    			$response['status'] = 'success';
    		}
    	}
    	return new JsonResponse($response);
    }
    
    /**
     * @Route("/load-influencers", name="inf_load_influencers", options={"expose"=true})
     */
    public function loadInfluencersAction(Request $request)
    {
    	$em = $this->getDoctrine()->getManager();
    	$data = $em->createQuery('SELECT u FROM InfluencerAppBundle:User u')// WHERE u.roles LIKE :role')
    		//->setParameter('role', '%ROLE_INFLUENCER%')
    		->getResult();
    	$influencers = [];
    	if ($data) {
    		foreach ($data as $influencer) {
    			$influencers[] = [
    				'id' => $influencer->getId(),
    				'firstName' => $influencer->getFirstName(),
    				'lastName' => $influencer->getLastName(),
    				'email' => $influencer->getEmail(),
    				'profileImage' => $influencer->getProfileImage(),
    				'profileCover' => $influencer->getProfileCover(),
    				'facebook' => $influencer->getFacebook(),
    				'google' => $influencer->getGoogle(),
    				'twitter' => $influencer->getTwitter(),
    				'instagram' => $influencer->getInstagram(),
    				'brief' => substr($influencer->getBrief(), 0, 150).(strlen($influencer->getBrief()) > 150?'...':''),
    				'lastLogin' => $influencer->getLastLogin()->format('d M @ h:ma'),
    			];
    		}
    	}
    	return new JsonResponse($influencers);
    }

}
