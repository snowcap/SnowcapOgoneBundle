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

use Twig_Environment;

use Ogone\FormGenerator\FormGenerator;
use Ogone\PaymentRequest;
use InvalidArgumentException;

class SimpleFormGenerator implements FormGenerator
{
    private $paymentRequest;

    private $showSubmitButton = true;

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
    public function render(PaymentRequest $paymentRequest)
    {
        $this->paymentRequest = $paymentRequest;

        /* @var \Symfony\Bundle\TwigBundle\Loader\FilesystemLoader $loader */
        $loader = $this->environment->getLoader();
        $loader->addPath($this->rootDir . '/Resources/SnowcapOgoneBundle/views/');
        $loader->addPath(__DIR__ . '/Resources/views/');
        $template = $this->environment->loadTemplate('form.html.twig');

        return $template->render(array('parameters' => $this->getParameters(), 'ogone_uri' => $this->getOgoneUri(), 'sha_sign' => $this->getShaSign(), 'show_submit' => $this->showSubmitButton, 'form_name' => $this->formName));
    }

    /**
     * @return mixed
     */
    protected function getParameters()
    {
        return $this->paymentRequest->toArray();
    }

    /**
     * @return mixed
     */
    protected function getOgoneUri()
    {
        return $this->paymentRequest->getOgoneUri();
    }

    /**
     * @return mixed
     */
    protected function getShaSign()
    {
        return $this->paymentRequest->getShaSign();
    }

    /**
     * @param bool $bool
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
