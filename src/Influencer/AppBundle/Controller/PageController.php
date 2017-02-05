<?php

namespace Influencer\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/page")
 */
class PageController extends Controller
{
	/**
	 * @Route("/home", name="inf_home", options={"expose"=true})
	 */
	public function homeAction(Request $request)
	{
		return $this->render('InfluencerAppBundle:Page:home.html.twig');
	}
	
	/**
	 * @Route("/login", name="inf_login", options={"expose"=true})
	 */
	public function loginAction(Request $request)
	{
		return $this->render('InfluencerAppBundle:Page:login.html.twig');
	}
	
	/**
	 * @Route("/signup", name="inf_signup", options={"expose"=true})
	 */
	public function signupAction(Request $request)
	{
		return $this->render('InfluencerAppBundle:Page:signup.html.twig');
	}
	
	/**
	 * @Route("/profile", name="inf_profile", options={"expose"=true})
	 */
	public function profileAction(Request $request)
	{
		return $this->render('InfluencerAppBundle:Page:signup.html.twig');
	}
}
