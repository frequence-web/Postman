<?php

require_once __DIR__.'/config/autoload.php';

use \Symfony\Component\Yaml\Yaml;
use \Postman\Kernel;
use \Postman\Configuration\Parser;

// DIC
$container = new Pimple;

// Set up event dispatcher
$container['event_dispatcher'] = $container->share(function() {
    return new \Symfony\Component\EventDispatcher\EventDispatcher;
});

// Parse config
$yaml = new Yaml;
$configParser = new Parser($container);
$configParser->parse($yaml->parse(file_get_contents(__DIR__.'/config/config.yml')));

// Set-up services

// Logger
$container['logger'] = $container->share(function() {
    $logger  = new Monolog\Logger('main');
    $logger->pushHandler(new Monolog\Handler\StreamHandler('php://stdout'));
    $logger->pushHandler(new Monolog\Handler\StreamHandler(sprintf(
        'file://%s',
        __DIR__.'/logs/debug.log'
    )));

    return $logger;
});

$container['postman.kernel'] = new Kernel($container);
$container['postman.request_handler'] = $container->share(function() use ($container) {
    return new \Postman\Request\RequestHandler($container);
});

// Connect events
$container['event_dispatcher']->addListener('postman.request', array($container['postman.request_handler'], 'handle'));

// Go
$container['postman.kernel']->boot();
