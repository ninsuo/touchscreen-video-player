<?php

namespace DigiLoginBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Security("has_role('ROLE_USER')")
 * @Route("/user")
 */
class UserController extends Controller
{
    /**
     * @Route("/", name="digi_manage")
     * @Template()
     */
    public function manageAction()
    {


        return [];
    }
}
