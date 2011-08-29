<?php

namespace Postman\Container;

class Container extends \Pimple
{
    protected $parameters = array();

    public function setParameter($key, $value)
    {
        $this->parameters[$key] = $value;
    }

    public function getParameter($key, $default = null)
    {
        return isset($this->parameters[$key]) ? $this->parameters[$key] : $default;
    }

    public function hasParameter($key)
    {
        return isset($this->parameters[$key]);
    }
}
