<?php

namespace Postman\Processor;

use \Symfony\Component\HttpFoundation\Response;
use \Symfony\Component\Process\PhpProcess;
use \Postman\Request\Request;
use \Postman\Container\ContainerAware;
use \Postman\Processor\Event\CallProcessorEvent;

class PHP extends ContainerAware implements ProcessorInterface
{
    public function supports(Request $request)
    {
        return strpos($request->getRequestUri(), '.php') !== false;
    }

    public function process(CallProcessorEvent $event)
    {
        $request = $event->getRequest();
        $response = $event->getResponse();

        if (!$this->supports($request)) {
            return;
        }

        $process = new PhpProcess($response->getContent(), $this->getParameter('basedir'), $request->server->all());

        $content = '';
        $process->run(function($type, $stdout) use ($response, &$content) {
            $content .= $stdout;
        });

        $response->headers->set('Content-Type', 'text/html');
        $response->setContent($content);
    }
}
