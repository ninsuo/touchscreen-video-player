<?php

namespace AppBundle\Command;

use AppBundle\Entity\Ip;
use BaseBundle\Base\BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

class IpAddCommand extends BaseCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('ip:add')
            ->setDescription('Whitelist an ip')
            ->addArgument('ip', InputArgument::REQUIRED, 'Ip to whitelist')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ip = $input->getArgument('ip');

        if (@ip2long($ip) === false) {
            $output->writeln("<error>IP {$ip} is invalid.</error>");
            return 1;
        }

        if ($this->getManager('AppBundle:Ip')->findOneByIp(ip2long($ip))) {
            $output->writeln("<error>IP {$ip} is already whitelisted.</error>");
            return 1;
        }

        $ipEntity = new Ip();
        $ipEntity->setIp(ip2long($ip));
        $this->getManager()->persist($ipEntity);
        $this->getManager()->flush();

        $output->writeln("Ip <info>{$ip}</info> is now whitelisted.");

       return 0;
    }
}
