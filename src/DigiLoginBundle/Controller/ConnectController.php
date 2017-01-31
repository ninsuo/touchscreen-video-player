<?php

namespace DigiLoginBundle\Controller;

use BaseBundle\Base\BaseController;
use DigiLoginBundle\Entity\DigiLogin;
use Symfony\Component\Form\FormInterface;
use DigiLoginBundle\Form\Type\DigiLoginConnectorType;
use HWI\Bundle\OAuthBundle\Security\Core\Authentication\Token\OAuthToken;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;

/**
 * @Route("/connect")
 */
class ConnectController extends BaseController
{
    /**
     * @Route("/", name="digi_connect")
     * @Template()
     */
    public function padAction(Request $request)
    {
        $manager = $this->getManager('DigiLoginBundle:DigiLogin');

        $data = new DigiLogin();

        $form = $this
           ->createForm(DigiLoginConnectorType::class, $data)
           ->handleRequest($request);

        // Invalid inputs
        if ($form->isSubmitted() && !$form->isValid()) {
            return $this->returnFailure($form, $data);
        }

        if ($form->isValid()) {

            // No matching login
            if (!$test = $manager->findOneByLogin($data->getLogin())) {
                return $this->returnFailure($form, $data);
            }

            // Revoked PIN code
            if (is_null($test->getPin())) {
                return $this->returnRevoked($form, $data);
            }

            $isValidPassword = (new BCryptPasswordEncoder(15))->isPasswordValid($test->getPin(), $data->getPin(), null);

            // User is logged and tests his credentials
            if ($user = $this->getUser()) {

                // Not the same user
                if (!$test->getUser()->isEqualTo($user)) {
                    return $this->returnFailure($form, $data);
                }

                // Invalid PIN code
                if (!$isValidPassword) {
                    return $this->returnFailure($form, $data);
                }

                return $this->returnSuccess($form, $data);
            }

            if (!$isValidPassword) {

                $test->setCurTries($test->getCurTries() + 1);

                // Too many tries, revoking PIN code
                if ($test->getCurTries() >= $test->getMaxTries()) {
                    $test->setPin(null);
                }

                $this->saveEntity($test);

                if (!$test->getPin()) {
                    return $this->returnRevoked($form, $data);
                }

                return $this->returnFailure($form, $data);
            }

            // Successful login
            $test->setCurTries(0);
            $this->saveEntity($test);

            // Symfony authentication
            $user = $this->get('base.oauth_user_provider')->loadUserByUsername($test->getUser()->getUsername());
            $token = new OAuthToken(null, $user->getRoles());
            $token->setUser($this->getRealEntity($user));
            $this->get('security.token_storage')->setToken($token);
            $this->get('session')->set('_security_main', serialize($token));
            $this->get('session')->save();

            return $this->returnSuccess($form, $data, 'home');
        }

        return [
            'form' => $form->createView(),
            'data' => $data,
        ];
    }

    protected function returnFailure(FormInterface $form, DigiLogin $data)
    {
        return [
            'form'    => $form->createView(),
            'data'    => $data,
            'failure' => true,
        ];
    }

    protected function returnRevoked(FormInterface $form, DigiLogin $data)
    {
        return [
            'form'    => $form->createView(),
            'data'    => $data,
            'revoked' => true,
        ];
    }

    protected function returnSuccess(FormInterface $form, DigiLogin $data, $route = null, $routeParams = [])
    {
        return [
            'form'        => $form->createView(),
            'data'        => $data,
            'success'     => true,
            'route'       => $route,
            'routeParams' => $routeParams,
        ];
    }
}
