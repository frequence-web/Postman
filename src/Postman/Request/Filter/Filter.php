<?php

namespace Postman\Request\Filter;

use \Postman\Request\Event\FilterRequestEvent;
use \Postman\Container\ContainerAware;

abstract class Filter extends ContainerAware implements FilterInterface
{
    public function listenFilterEvent(FilterRequestEvent $event)
    {
        $this->filter($event->getRequest());
    }
}
