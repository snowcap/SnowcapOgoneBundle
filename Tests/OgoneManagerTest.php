<?php

namespace Snowcap\OgoneBundle\Tests;

use Symfony\Component\EventDispatcher\EventDispatcher;

use Snowcap\OgoneBundle\OgoneManager;
use Snowcap\OgoneBundle\OgoneEvents;

class OgoneManagerTest extends \PHPUnit_Framework_TestCase {
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
            'somepspid',
            'test',
            '123456abc',
            'xyz987654'
        );
    }

    public function testPaymentResponse()
    {
        $this->eventDispatcher->addListener(OgoneEvents::SUCCESS, array($this, 'setWasCalled'));
        $this->ogoneManager->paymentResponse(array('SHASIGN' => sha1('somestring')));

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
}