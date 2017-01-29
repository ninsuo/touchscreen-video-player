<?php

namespace BaseBundle\Controller;

use BaseBundle\Base\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class UserController extends BaseController
{
    /**
     * Login failure action or login direct access.
     *
     * @Route("/login", name="login")
     * @Method({"GET"})
     */
    public function loginAction()
    {
        if ($this->getUser()) {
            return new RedirectResponse($this->generateUrl('home'));
        } else {
            return $this->forward('HWIOAuthBundle:Connect:connect');
        }
    }

    /**
     * Logout action.
     *
     * @Route("/logout", name="logout")
     * @Method({"GET"})
     */
    public function logoutAction(Request $request)
    {
        $this->container->get('security.token_storage')->setToken(null);

        return $this->goBack($request);
    }

    /**
     * Login action.
     *
     * @Route("/connect/{service}", name="connect")
     * @Method({"GET"})
     */
    public function connectAction(Request $request, $service)
    {
        $this->get('session')->set('referer', $request->headers->get('referer'));

        return $this->forward('HWIOAuthBundle:Connect:redirectToService', ['service' => $service]);
    }

    /**
     * Login success action.
     *
     * @Route("/welcome", name="welcome")
     * @Method({"GET"})
     */
    public function welcomeAction()
    {
        $referer = $this->get('session')->get('referer');
        if (is_null($referer)) {
            return new RedirectResponse($this->generateUrl('home'));
        }

        return new RedirectResponse($referer);
    }

    /**
     * User unsubscription confirmation.
     *
     * @Route("/unsuscribe", name="unsuscribe")
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function unsuscribeAction(Request $request)
    {
        if (!$this->getParameter('accounts_removable') || !$this->getUser()) {
            throw $this->createNotFoundException();
        }

        // CRSF
        $form = $this
           ->createFormBuilder()
           ->add('submit', 'submit', [
               'label' => 'base.unsuscribe.confirm',
               'attr'  => [
                   'class' => 'btn btn-danger',
               ],
           ])
           ->getForm()
        ;

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $user = $this->getUser();
                $em   = $this->get('doctrine.orm.entity_manager');
                $em->remove($user);
                $em->flush($user);

                return $this->forward('BaseBundle:User:logout');
            }

            return $this->goBack($request);
        }

        return $this->render('BaseBundle:User:unsuscribe.html.twig', [
               'form' => $form->createView(),
        ]);
    }
}
