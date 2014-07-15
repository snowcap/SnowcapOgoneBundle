<?php

namespace Snowcap\OgoneBundle\Command\DirectLink;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class OgoneDirectLinkMaintenanceCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->addArgument('payid', InputArgument::REQUIRED, "Payment Id of the order (PAYID).")
            ->addArgument('amount', InputArgument::OPTIONAL, "Order amount (in cents).");
    }

    protected function getOperation(InputInterface $input)
    {
        throw new \LogicException('You must override the getOperation() method in the concrete command class.');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $payid = $input->getArgument('payid');
        $amount = $input->getArgument('amount');

        $operation = $this->getOperation($input);

        $response = $this->getContainer()->get('snowcap_ogone.direct_link')
            ->maintenance($payid, null, $operation, $amount);

        if (!$response->isSuccessful()) {
            throw new \RuntimeException(sprintf("Error: %s", $response->getParam('NCERRORPLUS')));
        }

        $params = array(
            'ORDERID',
            'PAYID',
            'PAYIDSUB',
            'NCERROR',
            'NCERRORPLUS',
            'ACCEPTANCE',
            'STATUS',
            'AMOUNT',
            'CURRENCY',
        );

        foreach ($params as $param) {
            $output->writeln(sprintf("%11s: %s", $param, $response->getParam($param)));
        }
    }
}
