<?php

namespace Snowcap\OgoneBundle\Command\DirectLink;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class OgoneDirectLinkQueryCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('ogone:directlink:query')
            ->setDescription("Query the status of an Ogone order")
            ->setHelp(
<<<EOT
The <info>%command.name%</info> command allows you to query the status of an Ogone order automatically.
You can only query one payment at a time, and will only receive a limited amount of information about the order.

You can get information about an order by PayId (recommended):
    <info>%command.full_name% 33445566</info>

You can also get information about a specific history level of an order by specifying a PAYID/PAYSUBID combo:
    <info>%command.full_name% 33445566/1</info>

It is also possible, though discouraged, to get information about an order by OrderId:
    <info>%command.full_name% -o 123</info>
EOT
            );

        $this
            ->addArgument('payid', InputArgument::OPTIONAL, "Payment Id of the order (PAYID).")
            ->addOption('orderid', 'o', InputOption::VALUE_REQUIRED, "Order ID of the order (orderID).");

        parent::configure();
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        list($payid, $payidsub) = $this->extractPayidSubpayid($input->getArgument('payid'));

        $orderid = $input->getOption('orderid');

        if ((null === $payid && null === $orderid) || (null !== $payid && null !== $orderid)) {
            throw new \RuntimeException("Please provide either payid argument or --orderid option");
        }

        $response = $this->getContainer()->get('snowcap_ogone.direct_link')->query($payid, $payidsub, $orderid);

        if (!$response->isSuccessful()) {
            throw new \RuntimeException(sprintf("Error: %s", $response->getParam('NCERRORPLUS')));
        }

        $params = array(
            'orderID',
            'PAYID',
            'PAYIDSUB',
            'NCSTATUS',
            'NCERROR',
            'NCERRORPLUS',
            'ACCEPTANCE',
            'STATUS',
            'ECI',
            'amount',
            'currency',
            'PM',
            'BRAND',
            'CARDNO',
            'IP',
        );

        foreach ($params as $param) {
            $output->writeln(sprintf("%11s: %s", $param, $response->getParam($param)));
        }
    }

    /**
     * Extract PAYID/SUBPAYID from a single string
     *
     * Ex: "123123/1" => array("123123", "1")
     * Ex: "123123" => array("123123", null)
     *
     * @param $payid PAYID or PAYID/SUBPAYID combo
     *
     * @return array
     */
    private function extractPayidSubpayid($payid)
    {
        $payidsub = null;

        if (preg_match('/^(\d+)\/(\d+)$/', $payid, $matches)) {

            return array($matches[1], $matches[2]);
        }

        return array($payid, null);
    }
}
