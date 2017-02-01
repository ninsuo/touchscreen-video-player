<?php

namespace AppBundle\Command;

use BaseBundle\Tools\Gd;

class PrepareThumbnailsCommand extends AbstractAppCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('prepare:thumbnails')
            ->setDescription('Create .jpg thumbnails from your .mp4 videos')
        ;
    }

    protected function getFilter()
    {
        return '*.mp4';
    }

    protected function process($file)
    {
        $thumbnail = $file.'.png';
        if (!is_file($thumbnail)) {
            $input  = escapeshellarg($file);
            $output = escapeshellarg($thumbnail);
            exec("ffmpeg -ss 00:10:00 -i {$input} -vframes 1 -q:v 2 {$output}");
            Gd::save(Gd::resize(Gd::load($thumbnail), 640, 480), $thumbnail);
        }
    }
}
