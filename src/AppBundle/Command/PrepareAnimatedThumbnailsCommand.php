<?php

namespace AppBundle\Command;

use BaseBundle\Tools\Gd;

class PrepareAnimatedThumbnailsCommand extends AbstractAppCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('prepare:animated-thumbnails')
            ->setDescription('Create .gif thumbnails from your .mp4 videos')
        ;
    }

    protected function getFilter()
    {
        return '*.mp4';
    }

    protected function process($video)
    {
        $gif = $video.'.gif';
        if (!is_file($gif)) {
            $escapedVideo      = escapeshellarg($video);
            $escapedGif        = escapeshellarg($gif);
            $duration          = explode(':', exec("ffmpeg -i {$escapedVideo} 2>&1|grep -i duration|cut -d ' ' -f 4"));
            $seconds           = $duration[0] * 3600 + $duration[1] * 60 + intval($duration[2]);
            $timeBetweenFrames = intval($seconds / 7);
            for ($i = 1; $i <= 5; $i++) {
                $png = "{$video}_{$i}.png";
                $escapedPng = escapeshellarg($png);
                $time = gmdate("H:i:s", $i * $timeBetweenFrames);
                exec("ffmpeg -ss {$time} -i {$escapedVideo} -vframes 1 -q:v 2 {$escapedPng}");
                Gd::save(Gd::resize(Gd::load($png), 640, 480), $png);
            }
            $pattern = escapeshellarg("{$video}_*.png");
            exec("convert -delay 150 -loop 0 {$pattern} {$escapedGif}");
        }
    }
}
