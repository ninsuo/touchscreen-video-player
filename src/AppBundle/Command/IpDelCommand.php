<?php

namespace AppBundle\Command;

use AppBundle\Entity\Ip;
use BaseBundle\Base\BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

class IpDelCommand extends BaseCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('ip:del')
            ->setDescription('Remove an ip from whitelist')
            ->addArgument('ip', InputArgument::REQUIRED, 'Ip to remove')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ip = $input->getArgument('ip');

        if (@ip2long($ip) === false) {
            $output->writeln("<error>IP {$ip} is invalid.</error>");
            return 1;
        }

        $ipEntity = $this->getManager('AppBundle:Ip')->findOneByIp(ip2long($ip));
        if (!$ipEntity) {
            $output->writeln("<error>IP {$ip} is not whitelisted.</error>");
            return 1;
        }

        $this->getManager()->remove($ipEntity);
        $this->getManager()->flush();

        $output->writeln("Ip <info>{$ip}</info> has been removed.");

       return 0;
    }
}
