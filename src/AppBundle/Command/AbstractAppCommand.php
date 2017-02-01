<?php

namespace AppBundle\Command;

use BaseBundle\Base\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractAppCommand extends BaseCommand
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $directory = realpath($this->getParameter('kernel.root_dir') . '/../web/video');
        $filter = $this->getFilter();
        foreach (glob("{$directory}/{$filter}") as $file) {
            $this->process($file);
        }
    }

    abstract protected function getFilter();
    abstract protected function process($file);
}
