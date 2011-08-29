<?php

namespace Postman\Response\Provider;

use \Postman\Request\Event\GetResponseEvent;

interface ProviderInterface
{
  public function handle(GetResponseEvent $event);
}