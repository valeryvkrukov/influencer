<?php

namespace Influencer\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/admin")
 */
class AdminPageController extends Controller
{
    /**
     * @Route("/settings/signup", name="inf_admin_settings_signup", options={"expose"=true})
     */
    public function settingsSignupAction()
    {
        return $this->render('InfluencerAppBundle:AdminPage:settings/signup.html.twig');
    }
    
    /**
     * @Route("/users/list", name="inf_admin_users_list", options={"expose"=true})
     */
    public function usersListAction()
    {
    	return $this->render('InfluencerAppBundle:AdminPage:users/list.html.twig');
    }
    
    /**
     * @Route("/users/edit/{id}", name="inf_admin_users_edit", options={"expose"=true})
     */
    public function usersEditAction($id)
    {
    	return $this->render('InfluencerAppBundle:AdminPage:users/edit.html.twig', [
    		'id' => $id,
    	]);
    }
    
    /**
     * @Route("/users/create", name="inf_admin_users_create", options={"expose"=true})
     */
    public function usersCreateAction()
    {
    	return $this->render('InfluencerAppBundle:AdminPage:users/create.html.twig');
    }

}
