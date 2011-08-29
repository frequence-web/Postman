<?php

namespace Postman\Response\Provider;

use \Postman\Request\Request;
use \Symfony\Component\HttpFoundation\Response;
use \Postman\Container\ContainerAware;
use \Postman\Processor\Event\CallProcessorEvent;

abstract class Provider extends ContainerAware implements ProviderInterface
{
    protected function callProcessors(Request $request, Response $response)
    {
        $this->get('event_dispatcher')->dispatch('postman.call_processors', new CallProcessorEvent($request, $response));
    }
}