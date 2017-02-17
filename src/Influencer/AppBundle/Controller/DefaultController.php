<?php

namespace Influencer\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="inf_root", options={"expose"=true})
     */
    public function indexAction()
    {
        return $this->render('InfluencerAppBundle:Default:index.html.twig');
    }
    
    /**
     * @Route("/app", name="inf_app", options={"expose"=true})
     */
    public function appAction()
    {
    	return $this->render('InfluencerAppBundle:Default:app.html.twig');
    }
    
    /**
     * @Route("/header", name="inf_header", options={"expose"=true})
     */
    public function headerAction()
    {
    	return $this->render('InfluencerAppBundle:Default:header.html.twig');
    }
    
    /**
     * @Route("/header-blank", name="inf_header_blank", options={"expose"=true})
     */
    public function headerBlankAction()
    {
    	return $this->render('InfluencerAppBundle:Default:header-blank.html.twig');
    }
    
    /**
     * @Route("/sidebar", name="inf_sidebar", options={"expose"=true})
     */
    public function sidebarAction()
    {
    	return $this->render('InfluencerAppBundle:Default:sidebar.html.twig');
    }
    
    /**
     * @Route("/search", name="inf_search", options={"expose"=true})
     */
    public function searchAction()
    {
    	return $this->render('InfluencerAppBundle:Default:search.html.twig');
    }

    /**
     * @Route("/quickview", name="inf_quickview", options={"expose"=true})
     */
    public function quickviewAction()
    {
    	return $this->render('InfluencerAppBundle:Default:quickview.html.twig');
    }
    
    /**
     * @Route("/mobile-controls", name="inf_mobile_controls", options={"expose"=true})
     */
    public function mobileControlsAction()
    {
    	return $this->render('InfluencerAppBundle:Default:mobile_controls.html.twig');
    }

    /**
     * @Route("/footer", name="inf_footer", options={"expose"=true})
     */
    public function footerAction()
    {
    	return $this->render('InfluencerAppBundle:Default:footer.html.twig');
    }
    
    /**
     * @Route("/error/{code}", name="inf_error", options={"expose"=true})
     */
    public function errorAction($code)
    {
    	switch($code) {
    		case 10001:
    			$code = 500;
    			$message1 = 'Error';
    			$message2 = 'Invalid request parameters';
    			break;
    		default:
    			$message1 = 'Sorry but we couldnt find this page';
    			$message2 = 'This page you are looking for does not exsist';
    	}
    	return $this->render('InfluencerAppBundle:Default:error.html.twig', [
    		'code' => $code,
    		'message1' => $message1,
    		'message2' => $message2,
    	]);
    }
}
