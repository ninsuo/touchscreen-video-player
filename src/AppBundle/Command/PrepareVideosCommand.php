<?php

namespace AppBundle\Command;

class PrepareVideosCommand extends AbstractAppCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('prepare:videos')
            ->setDescription('Convert .avi videos from the video directory to compatible .mp4')
        ;
    }

    protected function getFilter()
    {
        return '*.avi';
    }

    protected function process($file)
    {
        $dst     = trim(str_replace('.avi', '.mp4', $file));
        $escaped = escapeshellarg(trim($file));
        if (!is_file($dst)) {
            $dst = escapeshellarg($dst);
            exec("ffmpeg -i {$escaped} -c:v libx264 -crf 24 -preset slow -c:a aac -strict experimental -b:a 192k -ac 2 {$dst}");
        }
    }
}
