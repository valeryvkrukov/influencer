<?php
namespace Influencer\AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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
	 * @Route("/update/{id}", name="inf_update_user", options={"expose"=true})
	 * @Method("POST")
	 */
	public function updateUser(Request $request, $id)
	{
		$input = json_decode($request->getContent());
		if ($input->id == $id) {
			unset($input->id);
			unset($input->role);
			$serializer = $this->get('serializer');
			$em = $this->getDoctrine()->getManager();
			$user = $em->getRepository('InfluencerAppBundle:User')->find($id);
			if ($user) {
				foreach ($input as $field => $value) {
					$setter = 'set'.ucfirst($field);
					if (!is_array($value)) {
						if (method_exists($user, $setter)) {
							$user->$setter($value);
						}
					} else {
						$em->getRepository('InfluencerAppBundle:User')->addIfNotExists($id, $field, $value, $serializer);
					}
				}
				$em->persist($user);
				$em->flush();
			}
		}
		return new JsonResponse($this->getUserData());
	}
	
	/**
	 * @Route("/home/{role}", name="inf_load_user_home", options={"expose"=true})
	 */
	public function getUserHome(Request $request, $role)
	{
		$userId = $this->getUser()->getId();
		$userRoles = $this->getUser()->getRoles();
		if ($role === 'admin' && in_array('ROLE_ADMIN', $userRoles)) {
			$data = $this->get('app.admin_data')->getDashboardData($userId);
		} elseif ($role === 'influencer' && in_array('ROLE_INFLUENCER', $userRoles)) {
			$data = $this->get('app.influencer_data')->getDashboardData($userId);
		} else {
			$data = get('app.client_data')->getDashboardData($userId);
		}
		return new JsonResponse($data);
	}
	
	/**
	 * @Route("/{role}/feed", name="inf_load_user_feed", options={"expose"=true})
	 */
	public function getUserFeedsAction(Request $request, $role)
	{
		$userId = $this->getUser()->getId();
		$userRoles = $this->getUser()->getRoles();
		if ($role === 'admin' && in_array('ROLE_ADMIN', $userRoles)) {
			$data = $this->get('app.admin_data')->getDashboardData($userId);
		} elseif ($role === 'influencer' && in_array('ROLE_INFLUENCER', $userRoles)) {
			$data = $this->get('app.influencer_data')->getFeeds($userId);
		} else {
			$data = get('app.client_data')->getDashboardData($userId);
		}
		return new JsonResponse($data);
	}
	
	/**
	 * @Route("/feeds", name="inf_feeds", options={"expose"=true})
	 */
	public function feedsAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$user = $this->getUser();
		$feeds = [];
		$networks = $em->getRepository('InfluencerAppBundle:Feed')->getUserNetworks($user);
		foreach ($networks as $n) {
			$data = $em->getRepository('InfluencerAppBundle:Feed')->loadSavedFeedsFor($user, $n['network'], 'array', 10);
			foreach ($data as $item) {
				$feeds[$n['network']][] = $item;
			}
		}
		
		return new JsonResponse($feeds); 
	}
	
	/**
	 * @Route("/get-stat/{network}/{id}", name="inf_user_get_stat", options={"expose"=true})
	 */
	public function getStatAction(Request $request, $network, $id)
	{
		$serv = 'get'.ucfirst($network).'Stats';
		$stat = $this->get('app.influencer_data')->$serv($id);
		return new JsonResponse($stat);
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
			$getter = 'get'.ucfirst($input->field);
			$value = isset($input->value)?$input->value:null;
			$user->$setter($value);
			$em->persist($user);
			$em->flush();
			return new JsonResponse([$input->field => $user->$getter()]);
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
			$em = $this->getDoctrine()->getManager();
			$usid = $em->getRepository('InfluencerAppBundle:User')->getUserSocialNetworkId($id, $network);
			$data = $this->get('app.feed_loader')->$getter($input->token, $usid);
			//return new JsonResponse($data);
			$em->getRepository('InfluencerAppBundle:Feed')->loadLatestForUser($data, $network, $id);
			$feeds = $em->getRepository('InfluencerAppBundle:Feed')->loadSavedFeedsFor($id, $network, 'array', 10);
			return new JsonResponse($feeds);
		}
	}
	
	/**
	 * @Route("/list", name="inf_admin_get_users_list", options={"expose"=true})
	 */
	public function adminGetUsersList(Request $request)
	{
		$roles = $this->getUser()->getRoles();
		if (in_array('ROLE_ADMIN', $roles)) {
			$em = $this->getDoctrine()->getManager();
			$users = $em->getRepository('InfluencerAppBundle:User')->getAllUsers();
			return new JsonResponse($users);
		}
	}
}
