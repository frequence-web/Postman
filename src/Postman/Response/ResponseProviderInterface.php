<?php

namespace Postman\Response;

use \Postman\Request\Event\GetResponseEvent;

interface ResponseProviderInterface
{
  public function handle(GetResponseEvent $event);
}