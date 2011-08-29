<?php

namespace Postman\Configuration;

use \Postman\Container\ContainerAware;
use \Postman\Configuration\Event\LoadConfigurationEvent;

/**
 * This class parse the server configuration to set-ip parameters and services.
 * It takes a PHP array, so it's format-agnostic (YML used, XML shouldn't be problematic)
 *
 * @throws InvalidArgumentException
 */
class Parser extends ContainerAware
{
    /**
     * Parse the whole configuration array
     *
     * @param array $config
     * @return void
     */
    public function parse(array $config)
    {
        $this->parseConfig($config['config']);
        $this->parseServices($config['services']);
    }

    /**
     * parse the "config:" section (parameters)
     *
     * @throws InvalidArgumentException
     * @param array $config
     * @param string $baseKey
     * @return void
     */
    private function parseConfig(array $config, $baseKey = '')
    {
        foreach ($config as $key => $value) {
            if (is_scalar($value)) {
                $this->container->setParameter($baseKey.$key, $value);
            }
            elseif (is_array($value)) {
                $this->parseConfig($value, $baseKey.$key.'.');
            }
            else {
                throw new \InvalidArgumentException('Unrecognized type');
            }
        }
    }

    /**
     * Parse the "services:" section
     *
     * @param array $config
     * @return void
     */
    private function parseServices(array $config)
    {
        foreach ($config as $type => $services) {
            switch ($type) {
                case 'response_providers':
                    $this->parseResponseProviders($services);
                    break;
                default:
                    $this->container['event_dispatcher']->dispatch(
                        'postman.load_configuration.'.$type,
                        new LoadConfigurationEvent($this->container, $services)
                    );
            }
        }
    }

    /**
     * Parse and set-up the response providers
     *
     * @param array $config
     * @return void
     */
    private function parseResponseProviders(array $config)
    {
        $container = $this->container;
        foreach ($config as $key => $provider) {

            $this->container['postman.response_provider.'.$key] = $this->container->share(
                function() use ($provider, $container) {
                    return new $provider($container);
                }
            );

            $this->container['event_dispatcher']->addListener(
                'postman.get_response',
                array($this->container['postman.response_provider.'.$key], 'handle')
            );

        }
    }
}
