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

    /**
     * @param string $name
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function getParameter($name)
    {
        if(!array_key_exists($name, $this->parameters)) {
            throw new \InvalidArgumentException(sprintf('The parameter "%s" does not exist in Ogone response parameters'));
        }

        return $this->parameters[$name];
    }
}