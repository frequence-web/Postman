<?php

namespace Postman\Response;

use \Postman\Request\Event\GetResponseEvent;
use \Postman\Container\ContainerAware;

class Error404ResponseProvider extends ContainerAware implements ResponseProviderInterface
{
  public function handle(GetResponseEvent $event)
  {
    $this->get('logger')->info('404 : Not found. URI: '.$event->getRequest()->getRequestUri());
    $response = new \Symfony\Component\HttpFoundation\Response('Erreur 404', 404);
    $event->setResponse($response);
  }

}
