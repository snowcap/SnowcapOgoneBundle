<?php

namespace Snowcap\OgoneBundle\Command\DirectLink\Maintenance;

use Ogone\DirectLink\DirectLinkMaintenanceRequest;
use Snowcap\OgoneBundle\Command\DirectLink\OgoneDirectLinkMaintenanceCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class OgoneDirectLinkCaptureCommand extends OgoneDirectLinkMaintenanceCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('ogone:directlink:capture')
            ->setDescription("Perform a data capture of an authorised order")
            ->setHelp(
<<<EOT
The <info>%command.name%</info> command allows you to perform a data capture (payment) of an authorised order
automatically (as opposed to manually in the back office).

Data captures are specifically for merchants who have configured their account/requests to perform the authorisation and
the data capture in two stages.

The <info>--amount</info> option is only required when the amount of the capture differs from the amount of the original
authorisation. However, its use is recommended in all cases. Ogone will check that the maintenance transaction amount is
not higher than the authorisation/payment amount.

Perform a full capture using the Payment ID:
    <info>%command.full_name% PAYID</info>

Perform a partial, final capture using the Payment ID:
    <info>%command.full_name% PAYID 1500</info>

Perform a partial capture using the Payment ID, leaving the transaction open for another potential capture:
    <info>%command.full_name% PAYID 1500 --open</info>
EOT
            );

        $this->addOption('open', null, InputOption::VALUE_NONE, "Leave transaction open for another potential capture.");
    }

    protected function getOperation(InputInterface $input)
    {
        return $input->getOption('open')
            ? DirectLinkMaintenanceRequest::OPERATION_CAPTURE_PARTIAL
            : DirectLinkMaintenanceRequest::OPERATION_CAPTURE_LAST_OR_FULL;
    }
}
