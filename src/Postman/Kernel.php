<?php
/**
 * The Kernel Class do some things...
 *
 * @author Yohan Giarelli <yohan@giarelli.org>
 */

namespace Postman;

use \Symfony\Component\Yaml\Yaml;
use \Postman\Request\Event\RequestEvent;
use \Postman\Container\ContainerAware;
use \Postman\Configuration\Parser;

abstract class Kernel extends ContainerAware
{
    protected $stream;

    abstract public function configure();

    abstract public function getConfigurationFormat();

    public function setup()
    {
        // TODO : implement XML configuration format
        switch ($this->getConfigurationFormat()) {
            default:
                $this->parseYamlConfig();
        }

        $this->setupLogger();
        $this->setupRequestHandler();

        $this->configure();
    }

    public function boot()
    {
        $this->stream = stream_socket_server(sprintf(
            'tcp://%s:%s',
            $this->getParameter('listen', '127.0.0.1'),
            $this->getParameter('port', 8080)
        ));

        $this->get('logger')->info(sprintf(
            'Started server. Listening on %s:%s',
            $this->getParameter('listen', '127.0.0.1'),
            $this->getParameter('port', 8080)
        ));

        while ($con = stream_socket_accept($this->stream, $this->getParameter('timeout'))) {
            $this->get('logger')->info('Connection accepted');
            $this->get('event_dispatcher')->dispatch('postman.request', new RequestEvent($con));
            fclose($con);
        }
    }

    protected function parseYamlConfig()
    {
        $yaml = new Yaml;
        $configParser = new Parser($this->container);
        $configParser->parse($yaml->parse(file_get_contents(__DIR__.'/../../config/config.yml')));
    }

    protected function setupLogger()
    {
        $this->container['logger'] = $this->container->share(function() {
            $logger  = new \Monolog\Logger('main');
            $logger->pushHandler(new \Monolog\Handler\StreamHandler('php://stdout'));
            $logger->pushHandler(new \Monolog\Handler\StreamHandler(sprintf(
                'file://%s',
                __DIR__.'/../../logs/debug.log'
            )));

            return $logger;
        });
    }

    protected function setupRequestHandler()
    {
        // Add to container
        $container = $this->container;
        $container['postman.request_handler'] = $container->share(function() use ($container) {
            return new \Postman\Request\RequestHandler($container);
        });

        // And connect event
        $container['event_dispatcher']->addListener(
            'postman.request',
            array($container['postman.request_handler'], 'handle')
        );
    }
}
