<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Ban;
use AppBundle\Entity\Ip;
use BaseBundle\Base\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

class DefaultController extends BaseController
{
    /**
     * @Route("/", name="home")
     * @Route("/", name="video_index")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        if (!$this->isWhitelisted($request->getClientIp())) {
            return $this->forward('AppBundle:Default:unauthorized');
        }

        $videoDir = realpath($this->get('kernel')->getRootDir().'/../web/video/');
        $videos   = glob("{$videoDir}/*.mp4");
        sort($videos);

        $normalized = [];
        foreach ($videos as $video) {
            $link      = substr($video, strlen($videoDir) + 1);

            $thumbnail = null;
            if (is_file("{$video}.gif")) {
                $thumbnail = "{$video}.gif";
            } elseif (is_file("{$video}.png")) {
                $thumbnail = "{$video}.png";
            }

            $normalized[] = [
                'link'      => substr($link, 0, -4),
                'thumbnail' => substr($thumbnail, strlen($videoDir) + 1),
            ];
        }

        $pager = new Pagerfanta(new ArrayAdapter($normalized));
        $pager->setNormalizeOutOfRangePages(true);
        $pager->setMaxPerPage(9);
        $pager->setCurrentPage($request->query->get('page', 1));

        return [
            'pager' => $pager,
        ];
    }

    /**
     * @Route("/play/{video}", name="video_play")
     * @Template()
     */
    public function playAction(Request $request, $video)
    {
        if (!$this->isWhitelisted($request->getClientIp())) {
            return $this->redirectToRoute('unauthorized');
        }

        return [
            'link'      => $link = str_replace('../', '', $video).'.mp4',
            'thumbnail' => $link.'.png',
        ];
    }

    public function isWhitelisted($ip)
    {
        return $this->getManager('AppBundle:Ip')->findOneByIp(ip2long($ip)) !== null;
    }

    /**
     * @Route("/unauthorized", name="unauthorized")
     * @Template()
     */
    public function unauthorizedAction(Request $request)
    {
        $this->getManager('AppBundle:Ban')->clean();

        $ip = $request->getClientIp();
        $ban = $this->getManager('AppBundle:Ban')->findOneByIp(ip2long($ip));
        $tries = $this->getParameter('tries') - ($ban ? $ban->getTries() : 0);

        $form = $this->createNamedFormBuilder('code')
            ->add('value', HiddenType::class)
            ->add('submit', SubmitType::class)
            ->getForm()
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $tries > 0) {
            if ($form->getData()['value'] == $this->getParameter('code')) {
                $ipEntity = new Ip();
                $ipEntity->setIp(ip2long($ip));
                $this->getManager()->persist($ipEntity);
                $this->getManager()->flush();

                return $this->redirectToRoute('home');
            } else {
                if ($ban) {
                    $ban->setTries($ban->getTries() + 1);
                    $ban->setTimestamp(time());
                } else {
                    $ban = new Ban();
                    $ban->setIp(ip2long($ip));
                    $ban->setTries(1);
                    $ban->setTimestamp(time());
                }
                $this->getManager()->persist($ban);
                $this->getManager()->flush($ban);

                $this->danger('app.unauthorized.invalid_password');

                return $this->redirectToRoute('home');
            }
        }

        return [
            'ip' => $ip,
            'tries' => $tries,
            'form' => $form->createView(),
        ];
    }
}
