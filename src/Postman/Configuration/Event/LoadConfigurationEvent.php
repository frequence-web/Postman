<?php

namespace Postman\Configuration\Event;

use \Symfony\Component\EventDispatcher\Event;

class LoadConfigurationEvent extends Event
{
    /**
     * @var \Pimple
     */
    protected $container;

    /**
     * @var array
     */
    protected $config;

    public function __construct(\Pimple $container, array $config)
    {
        $this->container = $container;
        $this->config = $config;
    }

    public function setContainer($container)
    {
        $this->container = $container;
    }

    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param array $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }
}
