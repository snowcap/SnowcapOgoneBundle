<?php

namespace Snowcap\OgoneBundle\Command\DirectLink\Maintenance;

use Ogone\DirectLink\DirectLinkMaintenanceRequest;
use Snowcap\OgoneBundle\Command\DirectLink\OgoneDirectLinkMaintenanceCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class OgoneDirectLinkRenewCommand extends OgoneDirectLinkMaintenanceCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('ogone:directlink:renew')
            ->setDescription("Perform a renewal of an authorisation")
            ->setHelp(
<<<EOT
The <info>%command.name%</info> command allows you to perform a renewal of authorisation, if the original authorisation
is no longer valid.

Authorisation renewals are specifically for merchants who have configured their account/requests to perform the
authorisation and the data capture in two stages.

The <info>--amount</info> option is only required when the amount of the maintenance differs from the amount of the
original authorisation. However, its use is recommended in all cases. Ogone will check that the maintenance transaction
amount is not higher than the authorisation/payment amount.

Perform an authorisation renewal using the Payment ID:
    <info>%command.full_name% PAYID</info>
EOT
            );
    }

    protected function getOperation(InputInterface $input)
    {
        return DirectLinkMaintenanceRequest::OPERATION_AUTHORISATION_RENEW;
    }
}
