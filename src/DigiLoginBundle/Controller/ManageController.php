<?php

namespace DigiLoginBundle\Controller;

use BaseBundle\Base\BaseController;
use DigiLoginBundle\Entity\DigiLogin;
use DigiLoginBundle\Form\Type\DigiLoginManagerType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;

/**
 * @Security("has_role('ROLE_USER')")
 * @Route("/user")
 */
class ManageController extends BaseController
{
    /**
     * @Route("/", name="digi_manage")
     * @Template()
     */
    public function manageAction(Request $request)
    {
        $manager = $this->getManager('DigiLoginBundle:DigiLogin');

        $data = $manager->findOneByUser($this->getUser());
        if (!$data) {
            $data = new DigiLogin();
            $data->setUser($this->getUser());
        }

        $form = $this
           ->createForm(DigiLoginManagerType::class, $data)
           ->handleRequest($request);

        if ($form->isValid()) {
            $test = $manager->findOneByLogin($data->getLogin());
            if ($test && (!$data->getId() || $test->getId() !== $data->getId())) {
                $form->get('login')->addError(
                   new FormError($this->trans('digilogin.manage.validation.login_already_used'))
                );
            } else {
                $data->setPin((new BCryptPasswordEncoder(15))->encodePassword($data->getPin(), null));
                $data->setCurTries(0);
                $this->saveEntity($data);
                $this->success('Your changes have been stored.');
                return new RedirectResponse(
                    $this->generateUrl('digi_manage')
                );
            }
        }

        return [
            'form' => $form->createView(),
            'data' => $data,
        ];
    }
}
