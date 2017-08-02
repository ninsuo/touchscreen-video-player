<?php

namespace AppBundle\Command;

use AppBundle\Entity\Ip;
use BaseBundle\Base\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

class IpListCommand extends BaseCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('ip:list')
            ->setDescription('List all whitelisted ips')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $users = $this->getManager("AppBundle:Ip")->findAll();

        $table = new Table($output);

        $table
           ->setHeaders(['ID', 'IP'])
           ->setRows(array_map(function(Ip $ip) {
               return [
                   $ip->getId(),
                   long2ip($ip->getIp()),
               ];
           }, $users))
           ->render()
        ;

       return 0;
    }
}
