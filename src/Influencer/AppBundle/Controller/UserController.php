<?php
namespace Influencer\AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Influencer\AppBundle\Controller\BaseController;

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
		$data = $em->getRepository('InfluencerAppBundle:Feed')->loadSavedFeedsFor($this->getUser(), null, 'array');
		$feeds = [];
		foreach ($data as $item) {
			$feeds[$item['network']][] = $item;
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
			//var_dump($data);die();
			$em = $this->getDoctrine()->getManager();
			$em->getRepository('InfluencerAppBundle:Feed')->loadLatestForUser($data, $network, $id);
			return new JsonResponse($data);
		}
	}
}
