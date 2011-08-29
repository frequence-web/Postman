<?php

namespace Postman\Processor;

use \Symfony\Component\HttpFoundation\Response;
use \Postman\Request\Request;
use \Postman\Processor\Event\CallProcessorEvent;

interface ProcessorInterface
{
  public function supports(Request $request);

  public function process(CallProcessorEvent $event);
}
