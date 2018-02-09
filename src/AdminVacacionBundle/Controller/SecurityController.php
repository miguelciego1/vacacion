<?php

namespace AdminVacacionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class SecurityController  extends Controller
{
  
    /**
     * @Route("/seguridad/login", name="login")
     */
    public function loginAction(Request $request)
    {
    $authenticationUtils = $this->get('security.authentication_utils');

    // get the login error if there is one
    $error = $authenticationUtils->getLastAuthenticationError();

    // last username entered by the user
    $lastUsername = $authenticationUtils->getLastUsername();

    return $this->render('AdminVacacionBundle:security:login.html.twig', array(
        'last_username' => $lastUsername,
        'error'         => $error,
    ));
    }

    /**
     * @Route("/seguridad/login_check", name="login_check")
     */
    public function loginCheckAction()
    {

    }

    /**
     * @Route("/seguridad/logout", name="logout")
     */
    public function logoutAction()
    {

    }

}
