<?php
namespace Influencer\AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Influencer\AppBundle\Controller\BaseController;
use Influencer\AppBundle\Entity\User;

/**
 * @Route("/admin")
 */
class AdminUserController extends BaseController
{
	/**
	 * @Route("/list-users", name="inf_admin_data_users_list", options={"expose"=true})
	 */
	public function getUsersListAction(Request $request)
	{
		$user = $this->getUser();
		if (in_array('ROLE_ADMIN', $user->getRoles())) {
			$input = json_decode($request->getContent());
			$field = isset($input->field)?$input->field:null;
			if ($field !== null) {
				$value = isset($input->filter)?$input->filter:null;
			} else {
				$value = null;
			}
			$em = $this->getDoctrine()->getManager();
			$users = $em->getRepository('InfluencerAppBundle:User')->getUsers($field, $value);
			
			return new JsonResponse($users);
		}
		return new JsonResponse(['error' => 'Access denied'], 403);
	}
	
	/**
	 * @Route("/edit-user/{id}", name="inf_admin_data_user_edit", options={"expose"=true})
	 */
	public function editUserAction(Request $request, $id)
	{
		$current = $this->getUser();
		if (in_array('ROLE_ADMIN', $current->getRoles())) {
			$em = $this->getDoctrine()->getManager();
			$user = $em->getRepository('InfluencerAppBundle:User')->getUserForAdminEdit($id);
			if ($user) {
				return new JsonResponse($user);
			}
		}
		return new JsonResponse(['error' => 'Access denied'], 403);
	}
	
	/**
	 * @Route("/update-user/{id}", name="inf_admin_data_user_update", options={"expose"=true})
	 */
	public function updateUserAction(Request $request, $id)
	{
		$current = $this->getUser();
		if (in_array('ROLE_ADMIN', $current->getRoles())) {
			$input = json_decode($request->getContent());
			$em = $this->getDoctrine()->getManager();
			$user = $em->getRepository('InfluencerAppBundle:User')->getUserForAdminEdit($id, 'object');
			if ($user) {
				$serializer = $this->get('serializer');
				foreach ($input as $field => $value) {
					$setter = 'set'.ucfirst($field);
					if (!is_array($value)) {
						if (method_exists($user, $setter)) {
							$user->$setter($value);
						}
					} elseif($field === 'roles') {
						$roles = $user->getRoles();
						foreach ($roles as $role) {
							$user->removeRole($role);
						}
						$user->setRoles($value);
					} else {
						$em->getRepository('InfluencerAppBundle:User')->addIfNotExists($id, $field, $value, $serializer);
					}
				}
				$em->persist($user);
				$em->flush();
				$user = $em->getRepository('InfluencerAppBundle:User')->getUserForAdminEdit($id);
				
				return new JsonResponse($user);
			}
		}
		return new JsonResponse(['error' => 'Access denied'], 403);
	}
	
	/**
	 * @Route("/new-user", name="inf_admin_data_user_create", options={"expose"=true})
	 */
	public function createUserAction(Request $request)
	{
		$current = $this->getUser();
		if (in_array('ROLE_ADMIN', $current->getRoles())) {
			$input = json_decode($request->getContent());
			$em = $this->getDoctrine()->getManager();
			try {
				$user = new User();
				$user->setUsername(md5($input->email.time()));
				$user->setPlainPassword('B!e281ckr');
				$serializer = $this->get('serializer');
				foreach ($input as $field => $value) {
					$setter = 'set'.ucfirst($field);
					if (!is_array($value)) {
						if (method_exists($user, $setter)) {
							$user->$setter($value);
						}
					} elseif($field === 'roles') {
						$user->setRoles($value);
					}/* else {
						$em->getRepository('InfluencerAppBundle:User')->addIfNotExists($id, $field, $value, $serializer);
					}*/
				}
				$em->persist($user);
				$em->flush();
			} catch(\Exception $e) {
				return new JsonResponse(['error' => 'User with same email already exists']);
			}
			$user = $em->getRepository('InfluencerAppBundle:User')->getUserForAdminEdit($user->getId());
			
			return new JsonResponse($user);
		}
		return new JsonResponse(['error' => 'Access denied'], 403);
	}
	
	/**
	 * @Route("/delete-user", name="inf_admin_data_user_delete", options={"expose"=true})
	 */
	public function deleteUserAction(Request $request)
	{
		$current = $this->getUser();
		if (in_array('ROLE_ADMIN', $current->getRoles())) {
			$input = json_decode($request->getContent());
			if (isset($input->user)) {
				$em = $this->getDoctrine()->getManager();
				$user = $em->getRepository('InfluencerAppBundle:User')->getUserForAdminEdit($input->user, 'object');
				$em->remove($user);
				$em->flush();
				return new JsonResponse(['message' => sprintf('User %s removed', $input->user)]);
			}
			return new JsonResponse(['error' => 'Invalid request parameter'], 409);
		}
		return new JsonResponse(['error' => 'Access denied'], 403);
	}
}