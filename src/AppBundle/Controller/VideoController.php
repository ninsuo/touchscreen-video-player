<?php

namespace AppBundle\Controller;

use AppBundle\Base\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

/**
 * @Route("/videos")
 *
 * SECURITY DISABLED TO MAKE SOME TESTS
 * (cookies not saved by lifefitness equipment)
 * 
 *********************************************
 * Security("has_role('ROLE_GROUP_VIDEO')")
 *********************************************
 *
 */
class VideoController extends BaseController
{
    /**
     * @Route("/", name="video_index")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $videoDir = realpath($this->get('kernel')->getRootDir().'/../web/video/');
        $videos   = glob("{$videoDir}/*.mp4");
        sort($videos);

        $normalized = [];
        foreach ($videos as $video) {
            $link      = substr($video, strlen($videoDir) + 1);
            $thumbnail = $video.'.jpg';
            if (!is_file($thumbnail)) {
                $input  = escapeshellarg($video);
                $output = escapeshellarg($thumbnail);
                @exec("ffmpeg -ss 00:10:00 -i {$input} -vframes 1 -q:v 2 {$output}");
                if (!is_file($thumbnail)) {
                    $thumbnail = null;
                }
            }
            $normalized[] = [
                'link'      => substr($link, 0, -4),
                'thumbnail' => substr($thumbnail, strlen($videoDir) + 1),
            ];
        }

        $pager = new Pagerfanta(new ArrayAdapter($normalized));
        $pager->setNormalizeOutOfRangePages(true);
        $pager->setMaxPerPage(6);
        $pager->setCurrentPage($request->query->get('page', 1));

        return [
            'pager' => $pager,
        ];
    }

    /**
     * @Route("/play/{video}", name="video_play")
     * @Template()
     */
    public function playAction($video)
    {
        return [
            'link'      => $link       = str_replace('../', '', $video).'.mp4',
            'thumbnail' => $link.'.jpg',
        ];
    }
}
