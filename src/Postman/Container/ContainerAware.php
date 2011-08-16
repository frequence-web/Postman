<?php
/**
 * The ContainerAware Class do some things...
 *
 * @author Yohan Giarelli <yohan@giarelli.org>
 */

namespace Postman\Container;

abstract class ContainerAware
{
  /**
   * @var \Pimple
   */
  protected $container;

  public function __construct(\Pimple $container)
  {
    $this->container = $container;
  }

  public function get($key)
  {
    return $this->container[$key];
  }

  /**
   * @param \Pimple $container
   */
  public function setContainer($container)
  {
    $this->container = $container;
  }

  /**
   * @return \Pimple
   */
  public function getContainer()
  {
    return $this->container;
  }


}
