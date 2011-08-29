<?php

namespace Postman\Request;

use \Postman\Request\Event\RequestEvent;

interface RequestHandlerInterface
{
  public function handle(RequestEvent $event);
}

