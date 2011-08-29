<?php

require_once __DIR__.'/config/autoload.php';

// DIC
$container = new \Postman\Container\Container();

// Set up event dispatcher
$container['event_dispatcher'] = $container->share(function() {
    return new \Symfony\Component\EventDispatcher\EventDispatcher;
});

// Run the server
$kernel = new \MyKernel($container);
$kernel->setup();
$kernel->boot();
