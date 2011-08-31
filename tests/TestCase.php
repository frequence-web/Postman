<?php

class TestCase extends \PHPUnit_Framework_TestCase
{
    protected $container;

    protected function setUp()
    {
        $this->container = new \Postman\Container\Container();
        $this->container['event_dispatcher'] = $this->container->share(function() {
            return new \Symfony\Component\EventDispatcher\EventDispatcher();
        });
    }
}