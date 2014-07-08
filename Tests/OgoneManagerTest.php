<?php

namespace Snowcap\OgoneBundle\Tests;

use Ogone\ShaComposer\AllParametersShaComposer;
use Symfony\Component\EventDispatcher\EventDispatcher;

use Snowcap\OgoneBundle\OgoneManager;
use Snowcap\OgoneBundle\OgoneEvents;

class OgoneManagerTest extends \PHPUnit_Framework_TestCase
{
    const PSPID = 'somepspid';
    const ENVIRONMENT = 'test';
    const SHA_IN = '123456abc';
    const SHA_OUT = 'xyz987654';

    /**
     * @var OgoneManager
     */
    private $ogoneManager;

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @var bool
     */
    private $wasCalled;

    protected function setUp()
    {
        $this->wasCalled = false;
        $this->eventDispatcher =  new EventDispatcher();

        $simpleFormGeneratorMock =  $this->getMockBuilder('Snowcap\OgoneBundle\FormGenerator\SimpleFormGenerator')
            ->disableOriginalConstructor()
            ->getMock();
        $simpleFormGeneratorMock
            ->expects($this->any())
            ->method('render')
            ->will($this->returnValue('<form method="post"></form>'));

        $this->ogoneManager = new OgoneManager(
            $this->eventDispatcher,
            $this->getMockBuilder('\Monolog\Logger')
                ->disableOriginalConstructor()
                ->getMock(),
            $simpleFormGeneratorMock,
            self::PSPID,
            self::ENVIRONMENT,
            self::SHA_IN,
            self::SHA_OUT
        );
    }

    public function testPaymentResponse()
    {
        $this->eventDispatcher->addListener(OgoneEvents::SUCCESS, array($this, 'setWasCalled'));

        $responseParameters = array(
            'STATUS' => \Ogone\PaymentResponse::STATUS_AUTHORISED,
        );
        $this->ogoneManager->paymentResponse($this->addShaSign($responseParameters));

        $this->assertEquals(true, $this->wasCalled);
    }

    public function testGetRequestForm()
    {
        $form = $this->ogoneManager->getRequestForm('fr', uniqid(), 'Pierre Vanliefland', 100);
        $this->assertInternalType('string', $form);
    }

    public function setWasCalled()
    {
        $this->wasCalled = true;
    }

    private function addShaSign(array $parameters)
    {
        return array_merge(
            $parameters,
            array(
                'SHASIGN' => (new AllParametersShaComposer(new \Ogone\Passphrase(self::SHA_OUT)))->compose($parameters),
            )
        );
    }
}
