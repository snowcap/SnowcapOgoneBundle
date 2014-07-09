<?php
/*
 * @author Nicolas Clavaud <nicolas@lrqdo.fr>
 */

namespace Snowcap\OgoneBundle;

use Ogone\DirectLink\DirectLinkMaintenanceRequest;
use Ogone\DirectLink\DirectLinkMaintenanceResponse;
use Ogone\DirectLink\DirectLinkQueryRequest;
use Ogone\DirectLink\DirectLinkQueryResponse;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Ogone\Passphrase;
use Ogone\ShaComposer\AllParametersShaComposer;
use Ogone\ParameterFilter\ShaInParameterFilter;
use Ogone\ParameterFilter\ShaOutParameterFilter;
use Ogone\FormGenerator\FormGenerator;
use Ogone\Ecommerce\EcommercePaymentRequest,
    Ogone\Ecommerce\EcommercePaymentResponse;

use Snowcap\OgoneBundle\Event\OgoneEvent;

class OgoneDirectLink
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
     * @var string
     */
    protected $apiUserId;

    /**
     * @var string
     */
    protected $apiPassword;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @param \Psr\Log\LoggerInterface $logger Logger
     * @param string $pspid Ogone PSPID
     * @param string $environment Ogone environment
     * @param string $shaIn Ogone SHA-IN key
     * @param string $shaOut Ogone SHA-OUT key
     * @param string $apiUserId Ogone API user ID
     * @param string $apiPassword Ogone API password
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        $pspid,
        $environment,
        $shaIn,
        $shaOut,
        $apiUserId,
        $apiPassword
    ) {
        $this->logger = $logger;
        $this->pspid = $pspid;
        $this->environment = $environment;
        $this->shaIn = new Passphrase($shaIn);
        $this->shaOut = new Passphrase($shaOut);
        $this->apiUserId = $apiUserId;
        $this->apiPassword = $apiPassword;
    }

    /**
     * @param string $payid Payment ID (PAYID)
     * @param string $orderid Order ID (orderID)
     * @param string $payidsub History level ID of the maintenance operation (PAYIDSUB)
     * @return DirectLinkQueryResponse
     * @throws \RuntimeException
     */
    public function query($payid, $payidsub = null, $orderid = null)
    {
        $shaComposer = new \Ogone\ShaComposer\AllParametersShaComposer($this->shaIn);
        $shaComposer->addParameterFilter(new \Ogone\ParameterFilter\ShaInParameterFilter);

        $request = new DirectLinkQueryRequest($shaComposer);

        switch ($this->environment) {
            case 'prod':
                $request->setOgoneUri(DirectLinkQueryRequest::PRODUCTION);
                break;
            default:
                $request->setOgoneUri(DirectLinkQueryRequest::TEST);
                break;
        }

        $request->setPspid($this->pspid);
        $request->setUserId($this->apiUserId);
        $request->setPassword($this->apiPassword);
        if (null !== $payid) {
            $request->setPayId($payid);
        }
        if (null !== $orderid) {
            $request->setOrderId($orderid);
        }
        if (null !== $payidsub) {
            $request->setPayIdSub($payidsub);
        }
        $request->validate();

        $parameters = $request->toArray();
        $parameters['SHASIGN'] = $request->getShaSign();

        $browser = new \Buzz\Browser(new \Buzz\Client\Curl);
        $response = $browser->post($request->getOgoneUri(), array(), http_build_query($parameters));

        if (200 !== $response->getStatusCode()) {
            throw new \RuntimeException("Ogone API did not return a valid response");
        }

        return new DirectLinkQueryResponse($response->getContent());
    }

    /**
     * @param string $payid Payment id (PAYID)
     * @param string $orderid Order id (orderID)
     * @param string $operation Operation
     * @param int $amount Amount
     * @return DirectLinkQueryResponse
     * @throws \RuntimeException
     */
    public function maintenance($payid, $orderid, $operation, $amount = null)
    {
        $shaComposer = new \Ogone\ShaComposer\AllParametersShaComposer($this->shaIn);
        $shaComposer->addParameterFilter(new \Ogone\ParameterFilter\ShaInParameterFilter);

        $request = new DirectLinkMaintenanceRequest($shaComposer);

        switch ($this->environment) {
            case 'prod':
                $request->setOgoneUri(DirectLinkMaintenanceRequest::PRODUCTION);
                break;
            default:
                $request->setOgoneUri(DirectLinkMaintenanceRequest::TEST);
                break;
        }

        $request->setPspid($this->pspid);
        $request->setUserId($this->apiUserId);
        $request->setPassword($this->apiPassword);
        if (null !== $payid) {
            $request->setPayId($payid);
        }
        if (null !== $orderid) {
            $request->setOrderId($orderid);
        }
        if (null !== $amount) {
            $request->setAmount($amount);
        }
        $request->setOperation($operation);
        $request->validate();

        $parameters = $request->toArray();
        $parameters['SHASIGN'] = $request->getShaSign();

        $browser = new \Buzz\Browser(new \Buzz\Client\Curl);
        $response = $browser->post($request->getOgoneUri(), array(), http_build_query($parameters));

        if (200 !== $response->getStatusCode()) {
            throw new \RuntimeException("Ogone API did not return a valid response");
        }

        return new DirectLinkMaintenanceResponse($response->getContent());
    }
}
