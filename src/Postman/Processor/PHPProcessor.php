<?php

namespace Postman\Processor;

use \Symfony\Component\HttpFoundation\Response;
use \Symfony\Component\Process\PhpProcess;
use \Postman\Request\Request;
use \Postman\Container\ContainerAware;

class PHPProcessor extends ContainerAware implements ProcessorInterface
{
  public function supports(Request $request)
  {
    return strpos($request->getRequestUri(), '.php') !== false;
  }

  public function prepare(Request $request)
  {
  }

  public function process(Request $request, Response $response, $content)
  {
    $this->prepare($request);

    $process = new PhpProcess($content, $this->get('config.basedir'), $request->server->all());

    $content = '';
    $process->run(function($type, $stdout) use ($response, &$content) {
        $content .= $stdout;
    });
    
    $response->headers->set('Content-Type', 'text/html');
    $response->setContent($content);
  }
}
