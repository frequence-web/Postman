<?php

namespace Postman\Processor;

use \Symfony\Component\HttpFoundation\Response;
use \Symfony\Component\Process\Process;
use \Symfony\Component\Process\ExecutableFinder;
use \Postman\Request\Request;
use \Postman\Container\ContainerAware;
use \Postman\Processor\Event\CallProcessorEvent;

class PHP extends ContainerAware implements ProcessorInterface
{
    public function supports(Request $request)
    {
        return strpos($request->getRequestUri(), '.php') !== false ||
               strpos($request->server->get('SCRIPT_FILENAME'), '.php') !== false;
    }

    public function process(CallProcessorEvent $event)
    {
        $request = $event->getRequest();
        $response = $event->getResponse();

        if (!$this->supports($request)) {
            return;
        }

        $request->server->set('REDIRECT_STATUS', true);

        $executableFinder = new ExecutableFinder();
        if (false === $php = $executableFinder->find('php-cgi')) {
            throw new \RuntimeException('Unable to find the PHP executable.');
        }
        $process = new Process($php, $this->getParameter('basedir'), $request->server->all(), $response->getContent());

        $content = '';
        $process->run(function($type, $stdout) use ($response, &$content) {
            $content .= $stdout;
        });

        $response->setContent($this->fixHeaders($response, $content));
    }

    private function fixHeaders(Response $response, $content)
    {
        $matches = null;
        if (0 !== preg_match_all('#(?<key>[-a-zA-Z0-9]+): (?<value>.*)\\r\\n#', $content, $matches)) {
            foreach ($matches['key'] as $index => $key) {
                $response->headers->set($key, $matches['value'][$index]);
                $content = preg_replace('#^.*\\r\\n(\\r\\n)?#', '', $content);
            }
        }

        return $content;
    }
}
