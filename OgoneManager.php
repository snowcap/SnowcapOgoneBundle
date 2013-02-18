<?php
/*
 * This file is part of the Snowcap OgoneBundle package.
 *
 * (c) Snowcap <shoot@snowcap.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Snowcap\OgoneBundle;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Monolog\Logger;
use Ogone\Passphrase;
use Ogone\PaymentRequest;
use Ogone\PaymentResponse;
use Ogone\ShaComposer\AllParametersShaComposer;
use Ogone\ParameterFilter\ShaInParameterFilter;
use Ogone\ParameterFilter\ShaOutParameterFilter;
use Ogone\FormGenerator\FormGenerator;

use Snowcap\OgoneBundle\Event\OgoneEvent;

class OgoneManager
{
    /**
     * @var string
     */
    protected $pspid;

    /**
     * @var string
     */
    protected $environment;

    /**
     * @var \Ogone\Passphrase
     */
    protected $shaIn;

    /**
     * @var \Ogone\Passphrase
     */
    protected $shaOut;

    /**
     * @var array
     */
    protected $options = array();

    /**
     * @var array
     */
    protected $listeners = array();

    /**
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @var \Monolog\Logger
     */
    protected $logger;

    /**
     * @var \Ogone\FormGenerator\FormGenerator
     */
    protected $formGenerator;

    /**
     * @param \Monolog\Logger $logger
     * @param \Ogone\FormGenerator\FormGenerator $formGenerator
     * @param $pspid
     * @param $environment
     * @param $shaIn
     * @param $shaOut
     * @param array $options
     */
    public function __construct(
        EventDispatcher $eventDispatcher,
        Logger $logger,
        FormGenerator $formGenerator,
        $pspid,
        $environment,
        $shaIn,
        $shaOut,
        $options = array()
    ) {
        // TODO: use config validation
        if ($pspid === "") {
            throw new \Exception('No PSPID defined for Ogone');
        }
        if ($environment !== "test" && $environment !== "prod") {
            throw new \Exception(sprintf('No valid Ogone environment ("test" or "prod"), "%s" given', $environment));
        }
        if ($shaIn === "") {
            throw new \Exception('No SHA-IN passphrase defined for Ogone');
        }
        if ($shaOut === "") {
            throw new \Exception('No SHA-OUT passphrase defined for Ogone');
        }

        $this->eventDispatcher = $eventDispatcher;
        $this->logger = $logger;
        $this->formGenerator = $formGenerator;
        $this->pspid = $pspid;
        $this->environment = $environment;
        $this->shaIn = new Passphrase($shaIn);
        $this->shaOut = new Passphrase($shaOut);
        $this->options = $options;
    }

    /**
     * @param $locale
     * @param $orderId
     * @param $customerName
     * @param $amount
     * @param string $currency
     * @param array $options
     * @return string
     */
    public function getRequestForm($locale, $orderId, $customerName, $amount, $currency = "EUR", $options = array())
    {
        $passphrase = $this->shaIn;
        $shaComposer = new AllParametersShaComposer($passphrase);
        $shaComposer->addParameterFilter(new ShaInParameterFilter); //optional

        $paymentRequest = new PaymentRequest($shaComposer);

        switch ($this->environment) {
            case 'prod':
                $paymentRequest->setOgoneUri(PaymentRequest::PRODUCTION);
                break;
            default:
                $paymentRequest->setOgoneUri(PaymentRequest::TEST);
                break;
        }

        $paymentRequest->setPspid($this->pspid);
        $paymentRequest->setCn($customerName);
        $paymentRequest->setOrderid($orderId);
        $paymentRequest->setAmount($amount);
        $paymentRequest->setCurrency($currency);

        // setting options defined in config
        foreach ($this->options as $option => $value) {
            $setter = "set" . $option;
            $paymentRequest->$setter($value);
        }

        // setting options defined in method call
        foreach ($options as $option => $value) {
            $setter = "set" . $option;
            $paymentRequest->$setter($value);
        }

        $paymentRequest->setLanguage($this->localeToIso($locale));
        $paymentRequest->validate();

        return $this->formGenerator->render($paymentRequest);
    }

    /**
     * @param array $parameters
     */
    public function paymentResponse(array $parameters)
    {
        $paymentResponse = new PaymentResponse($parameters);

        $passphrase = $this->shaOut;
        $shaComposer = new AllParametersShaComposer($passphrase);
        $shaComposer->addParameterFilter(new ShaOutParameterFilter); //optional

        if ($paymentResponse->isValid($shaComposer) && $paymentResponse->isSuccessful()) {
            $event = new OgoneEvent($parameters);
            $this->eventDispatcher->dispatch(OgoneEvents::SUCCESS, $event);

            // handle payment confirmation
            $this->logger->info('Ogone payment success');

	    return true;
        } else {
            $event = new OgoneEvent($parameters);
            $this->eventDispatcher->dispatch(OgoneEvents::ERROR, $event);

            $this->logger->warn('Ogone payment failure', $parameters);
        }
	return false;
    }

    /**
     * @param string $locale
     * @return string
     */
    private function localeToIso($locale)
    {
        switch ($locale) {
       	    case 'de':
                return 'de_DE';
                break;
            case 'fr':
                return 'fr_FR';
                break;
            case 'nl':
                return 'nl_NL';
                break;
            case 'en':
                return 'en_US';
                break;
            default:
                return $locale;
                break;
        }
    }
}
