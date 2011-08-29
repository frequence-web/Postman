<?php
/**
 * The ContainerAware Class do some things...
 *
 * @author Yohan Giarelli <yohan@giarelli.org>
 */

namespace Postman\Container;

use \Postman\Container\Container;

abstract class ContainerAware
{
    /**
    * @var Container
    */
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function get($key)
    {
        return $this->container[$key];
    }

    public function getParameter($key, $default = null)
    {
        return $this->container->getParameter($key, $default);
    }

    public function hasParameter($key)
    {
        return $this->container->hasParameter($key);
    }

    /**
    * @param Container $container
    */
    public function setContainer($container)
    {
        $this->container = $container;
    }

    /**
    * @return Container
    */
    public function getContainer()
    {
        return $this->container;
    }
}
