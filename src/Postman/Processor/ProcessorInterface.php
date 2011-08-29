<?php

namespace Postman\Processor;

use \Symfony\Component\HttpFoundation\Response;
use \Postman\Request\Request;

interface ProcessorInterface
{
  public function supports(Request $request);

  public function prepare(Request $request);

  public function process(Request $request, Response $response, $content);
}
