<?php

namespace Influencer\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use Influencer\AppBundle\Controller\BaseController;

/**
 * @Route("/page")
 */
class PageController extends BaseController
{
	/**
	 * @Route("/home", name="inf_home", options={"expose"=true})
	 */
	public function homeAction(Request $request)
	{
		switch ($request->query->get('role')) {
			case 'admin':
				$tpl = 'admin/home';
				break;
			case 'influencer':
				$tpl = 'influencer/home';
				break;
			case 'client':
				$tpl = 'client/home';
				break;
			default:
				$tpl = 'home';
		}
		return $this->render('InfluencerAppBundle:Page:'.$tpl.'.html.twig');
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
		switch ($request->query->get('role')) {
			case 'admin':
				$tpl = 'admin/profile';
				break;
			case 'influencer':
				$tpl = 'influencer/profile';
				break;
			case 'client':
				$tpl = 'client/profile';
				break;
			default:
				$tpl = 'home';
		}
		return $this->render('InfluencerAppBundle:Page:admin/'.$tpl.'.html.twig');
	}
	
	/**
	 * @Route("/campaign", name="inf_campaign", options={"expose"=true})
	 */
	public function campaignAction(Request $request)
	{
		return $this->render('InfluencerAppBundle:Page:campaign.html.twig');
	}
}
