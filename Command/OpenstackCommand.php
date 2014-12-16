<?php
/**
 * Created by PhpStorm.
 * User: stijnvoss
 * Date: 07/12/14
 * Time: 11:40
 */
namespace ISTI\Image\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use OpenCloud\OpenStack;
class OpenstackCommand extends  ContainerAwareCommand {
    protected function configure()
    {

        $this
            ->setName('openstack:test-prepare')
            ->setDescription('Test swift');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $this->getContainer()->getParameter('openstack_username');
        $password = $this->getContainer()->getParameter('openstack_password');
        $tenantId = $this->getContainer()->getParameter('openstack_tenant_id');
        $endpoint = $this->getContainer()->getParameter('openstack_id_url');
        $private = $this->getContainer()->getParameter('openstack_os_image_private_container');
        $public = $this->getContainer()->getParameter('openstack_os_image_public_container');
        $connection = new OpenStack(
            $endpoint,
            array('username' => $username,
                'password' => $password,
                'tenantName' => $tenantId)
        );
        $output->writeln("Identification succesfull");
        $ostore = $connection->objectStoreService("swift", "NL", "publicURL");
        $output->writeln("Reached object store");


        $ostore->createContainer($public);
        $ostore->createContainer($private);
    }
} 