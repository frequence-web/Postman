<?php

namespace Postman\Request\Filter;

use \Symfony\Component\HttpFoundation\Request;
use \Postman\Request\Event\FilterRequestEvent;

interface FilterInterface
{
    public function filter(Request $request);

    public function listenFilterEvent(FilterRequestEvent $event);
}
