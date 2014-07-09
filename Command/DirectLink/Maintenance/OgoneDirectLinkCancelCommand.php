<?php

namespace Snowcap\OgoneBundle\Command\DirectLink\Maintenance;

use Ogone\DirectLink\DirectLinkMaintenanceRequest;
use Snowcap\OgoneBundle\Command\DirectLink\OgoneDirectLinkMaintenanceCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class OgoneDirectLinkCancelCommand extends OgoneDirectLinkMaintenanceCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('ogone:directlink:cancel')
            ->setDescription("Perform a cancellation of an authorised order")
            ->setHelp(
<<<EOT
The <info>%command.name%</info> command allows you to cancel an authorisation.

Authorisation cancellations are specifically for merchants who have configured their account/requests to perform the
authorisation and the data capture in two stages.

The <info>--amount</info> option is only required when the amount of the maintenance differs from the amount of the
original authorisation. However, its use is recommended in all cases. Ogone will check that the maintenance transaction
amount is not higher than the authorisation/payment amount.

Cancel an authorisation using the Payment ID:
    <info>%command.full_name% PAYID</info>

Cancel an authorisation using the Payment ID, leaving the transaction open for further potential maintenance operations:
    <info>%command.full_name% PAYID --open</info>
EOT
            );

        $this->addOption(
            'open',
            null,
            InputOption::VALUE_NONE,
            "Leave transaction open for another potential maintenance operation."
        );
    }

    protected function getOperation(InputInterface $input)
    {
        return $input->getOption('open')
            ? DirectLinkMaintenanceRequest::OPERATION_AUTHORISATION_DELETE
            : DirectLinkMaintenanceRequest::OPERATION_AUTHORISATION_DELETE_AND_CLOSE;
    }
}
