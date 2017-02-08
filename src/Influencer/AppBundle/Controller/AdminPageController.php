<?php

namespace Influencer\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class AdminPageController extends Controller
{
    /**
     * @Route("/settings/signup", name="inf_admin_settings_signup", options={"expose"=true})
     */
    public function settingsSignupAction()
    {
        return $this->render('InfluencerAppBundle:AdminPage:settings/signup.html.twig');
    }

}
