<?php
namespace Influencer\AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

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
}
