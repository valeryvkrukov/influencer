<?php
namespace Influencer\AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Influencer\AppBundle\Controller\BaseController;
use GuzzleHttp;

/**
 * @Route("/user")
 */
class UserController extends BaseController
{
	/**
	 * @Route("/me", name="inf_me", options={"expose"=true})
	 */
	public function meAction(Request $request)
	{
		$user = $this->getUserData();
		return new JsonResponse($user);
	}
	
	/**
	 * @Route("/feeds", name="inf_feeds", options={"expose"=true})
	 */
	public function feedsAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$userId = $this->getUser();
		$feeds = [];
		$networks = $em->getRepository('InfluencerAppBundle:Feed')->getUserNetworks($userId);
		foreach ($networks as $n) {
			$data = $em->getRepository('InfluencerAppBundle:Feed')->loadSavedFeedsFor($userId, $n['network'], 'array', 10);
			foreach ($data as $item) {
				$feeds[$n['network']][] = $item;
			}
		}
		
		return new JsonResponse($feeds); 
	}
	
	/**
	 * @Route("/update-field", name="inf_user_update_field", options={"expose"=true})
	 */
	public function updateFieldAction(Request $request)
	{
		$input = json_decode($request->getContent());
		if (isset($input->user) && isset($input->field)) {
			$em = $this->getDoctrine()->getManager();
			$user = $this->getUser();
			$setter = 'set'.ucfirst($input->field);
			$value = isset($input->value)?$input->value:null;
			$user->$setter($value);
			$em->persist($user);
			$em->flush();
			return new JsonResponse($user);
		}
	}
	
	/**
	 * @Route("/load-influencer-feed/{network}/{id}", name="inf_load_feeds", options={"expose"=true})
	 */
	public function loadFeedsAction(Request $request, $network, $id)
	{
		$input = json_decode($request->getContent());
		if (isset($input->token)) {
			$getter = 'load'.ucfirst($network).'Feed';
			$data = $this->get('app.feed_loader')->$getter($input->token, $id);
			$em = $this->getDoctrine()->getManager();
			$em->getRepository('InfluencerAppBundle:Feed')->loadLatestForUser($data, $network, $id);
			$feeds = $em->getRepository('InfluencerAppBundle:Feed')->loadSavedFeedsFor($id, $network, 'array', 10);
			return new JsonResponse($data);
		}
	}
	
}
