<?php

require_once __DIR__.'/config/autoload.php';

use \Postman\Kernel;
use \Symfony\Component\Yaml\Yaml;

// DIC
$container = new Pimple;

// Parsing config
$yaml = new \Symfony\Component\Yaml\Yaml();
$config = $yaml->parse(file_get_contents(__DIR__.'/config/config.yml'));

$parseConfig = function(array $config, $baseKey = '') use ($container, &$parseConfig) {
  foreach ($config as $key => $value) {
    if (is_scalar($value)) {
      $container[$baseKey.$key] = $value;
      if ($baseKey == 'services.response_providers.') {
        $container['postman.response_provider.'.$key] = $container->share(function() use ($container, $value) {
          return new $value($container);
        });
        $container['event_dispatcher']->addListener(
          'postman.get_response',
          array($container['postman.response_provider.'.$key], 'handle')
        );
      }
    }
    elseif (is_array($value)) {
      $parseConfig($value, $baseKey.$key.'.');
    }
    else {
      throw new InvalidArgumentException('Unrecognized type');
    }
  }
};

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
$container['event_dispatcher'] = $container->share(function() {
  return new \Symfony\Component\EventDispatcher\EventDispatcher;
});

// Connect events
$container['event_dispatcher']->addListener('postman.request', array($container['postman.request_handler'], 'handle'));

// Apply config
$parseConfig($config);

// Go
$container['postman.kernel']->boot();