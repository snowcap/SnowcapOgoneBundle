<?php
/*
 * This file is part of the Snowcap OgoneBundle package.
 *
 * (c) Snowcap <shoot@snowcap.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Snowcap\OgoneBundle\FormGenerator;

use Twig_Environment;

use Ogone\FormGenerator\FormGenerator;
use Ogone\PaymentRequest;
use Ogone\Ecommerce\EcommercePaymentRequest;

class SimpleFormGenerator implements FormGenerator
{
    /**
     * @var PaymentRequest
     */
    private $paymentRequest;

    /**
     * @var bool
     */
    private $showSubmitButton = true;

    /**
     * @var string
     */
    private $formName = 'ogone';

    /**
     * @var Twig_Environment
     */
    private $environment;

    /**
     * @var string
     */
    private $rootDir;

    /**
     * @param \Twig_Environment $environment
     */
    public function __construct(Twig_Environment $environment, $rootDir)
    {
        $this->environment = $environment;
        $this->rootDir = $rootDir;
    }

    /**
     * @param PaymentRequest $paymentRequest
     *
     * @return string
     */
    public function render(EcommercePaymentRequest $paymentRequest)
    {
        $this->paymentRequest = $paymentRequest;

        // TODO: make this part slightly more flexible or generate only hidden fields ?
        $template = $this->environment->loadTemplate('SnowcapOgoneBundle::form.html.twig');

        return $template->render(array('parameters' => $this->getParameters(), 'ogone_uri' => $this->getOgoneUri(), 'sha_sign' => $this->getShaSign(), 'show_submit' => $this->showSubmitButton, 'form_name' => $this->formName));
    }

    /**
     * @return array
     */
    protected function getParameters()
    {
        return $this->paymentRequest->toArray();
    }

    /**
     * @return string
     */
    protected function getOgoneUri()
    {
        return $this->paymentRequest->getOgoneUri();
    }

    /**
     * @return string
     */
    protected function getShaSign()
    {
        return $this->paymentRequest->getShaSign();
    }

    /**
     * @param bool
     */
    public function showSubmitButton($bool = true)
    {
        $this->showSubmitButton = $bool;
    }

    /**
     * @param string $formName
     */
    public function setFormName($formName)
    {
        $this->formName = $formName;
    }
}
