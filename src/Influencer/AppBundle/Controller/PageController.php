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
	 * @Route("/home/{role}", name="inf_home", options={"expose"=true})
	 */
	public function homeAction(Request $request, $role = null)
	{
		switch ($role) {
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
	 * @Route("/feeds/{role}", name="inf_feed", options={"expose"=true})
	 */
	public function feedsAction(Request $request, $role = null)
	{
		switch ($role) {
			case 'admin':
				$tpl = 'admin/feeds';
				break;
			case 'influencer':
				$tpl = 'influencer/feeds';
				break;
			case 'client':
				$tpl = 'client/feeds';
				break;
			default:
				$tpl = 'feeds';
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
	 * @Route("/profile/{role}", name="inf_profile", options={"expose"=true})
	 */
	public function profileAction(Request $request, $role = null)
	{
		switch ($role) {
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
				$tpl = 'profile';
		}
		return $this->render('InfluencerAppBundle:Page:'.$tpl.'.html.twig');
	}
	
	/**
	 * @Route("/{role}/profile/main", name="inf_profile_main", options={"expose"=true})
	 */
	public function profileMainAction(Request $request, $role = null)
	{
		switch ($role) {
			case 'admin':
				$tpl = 'admin/profile-main';
				break;
			case 'influencer':
				$tpl = 'influencer/profile-main';
				break;
			case 'client':
				$tpl = 'client/profile-main';
				break;
			default:
				$tpl = 'profile-main';
		}
		return $this->render('InfluencerAppBundle:Page:'.$tpl.'.html.twig');
	}
	
	/**
	 * @Route("/{role}/profile/audience", name="inf_profile_audience", options={"expose"=true})
	 */
	public function profileAudienceAction(Request $request, $role = null)
	{
		switch ($role) {
			case 'admin':
				$tpl = 'admin/profile-audience';
				break;
			case 'influencer':
				$tpl = 'influencer/profile-audience';
				break;
			case 'client':
			default:
				$tpl = 'error';
		}
		return $this->render('InfluencerAppBundle:Page:'.$tpl.'.html.twig');
	}
	
	/**
	 * @Route("/{role}/profile/campaigns", name="inf_profile_campaigns", options={"expose"=true})
	 */
	public function profileCampaignsAction(Request $request, $role = null)
	{
		switch ($role) {
			case 'admin':
				$tpl = 'admin/profile-campaigns';
				break;
			case 'influencer':
				$tpl = 'influencer/profile-campaigns';
				break;
			case 'client':
			default:
				$tpl = 'error';
		}
		return $this->render('InfluencerAppBundle:Page:'.$tpl.'.html.twig');
	}
	
	/**
	 * @Route("/{role}/profile/socials", name="inf_profile_socials", options={"expose"=true})
	 */
	public function profileSocialsAction(Request $request, $role = null)
	{
		switch ($role) {
			case 'admin':
				$tpl = 'admin/profile-socials';
				break;
			case 'influencer':
				$tpl = 'influencer/profile-socials';
				break;
			case 'client':
			default:
				$tpl = 'error';
		}
		return $this->render('InfluencerAppBundle:Page:'.$tpl.'.html.twig');
	}
	
	/**
	 * @Route("/campaign", name="inf_campaign", options={"expose"=true})
	 */
	public function campaignAction(Request $request)
	{
		return $this->render('InfluencerAppBundle:Page:campaign.html.twig');
	}
}
