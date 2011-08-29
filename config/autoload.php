<?php

require_once __DIR__ . '/../vendor/symfony/src/Symfony/Component/ClassLoader/UniversalClassLoader.php';

$loader = new \Symfony\Component\ClassLoader\UniversalClassLoader;

$loader->registerNamespaces(array(
    'Postman' => __DIR__.'/../src',
    'Symfony' => __DIR__.'/../vendor/symfony/src',
    'Monolog' => __DIR__.'/../vendor/monolog/src'
));

$loader->registerPrefixFallbacks(array(__DIR__. '/../vendor/pimple/lib', __DIR__,));

$loader->register();
