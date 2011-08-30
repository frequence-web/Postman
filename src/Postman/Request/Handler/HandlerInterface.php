<?php

namespace Postman\Request\Handler;

use \Postman\Request\Event\RequestEvent;

interface HandlerInterface
{
  public function handle(RequestEvent $event);
}

