<?php

namespace Snowcap\OgoneBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class OgoneEvent extends Event
{
    /**
     * @var array
     */
    private $parameters;

    /**
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}