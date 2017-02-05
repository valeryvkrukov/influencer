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
    		[
    			'name' => 'Event',
    			'tag' => 'event'
    		],
    		[
    			'name' => 'Video',
    			'tag' => 'video'
    		],
    		[
    			'name' => 'Photoshoot',
    			'tag' => 'photo'
    		],
    		[
    			'name' => 'Social media',
    			'tag' => 'social'
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
    		$user = $em->createQuery('SELECT u FROM StakeholdersApiBundle:User u WHERE u.username = :username')
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
    		$user = $em->createQuery('SELECT u FROM StakeholdersApiBundle:User u WHERE u.email = :email')
    			->setParameter('email', $email)
    			->getOneOrNullResult();
    		if ($user === null) {
    			$response['status'] = 'success';
    		}
    	}
    	return new JsonResponse($response);
    }

}
