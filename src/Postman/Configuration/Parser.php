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

        foreach ($config as $type => $subConfig) {
            switch ($type) {
                case 'config':
                case 'services':
                    break;
                default:
                    $this->container['event_dispatcher']->dispatch(
                        'postman.load_configuration.'.$type,
                        new LoadConfigurationEvent($this->container, $subConfig)
                    );
            }
        }
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
                case 'processors':
                    $this->parseProcessors($services);
                    break;
                case 'filters':
                    $this->parseFilters($services);
                    break;
                default:
                    $this->container['event_dispatcher']->dispatch(
                        'postman.load_configuration.service.'.$type,
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

    /**
     * Parse and set-up processors
     *
     * @param array $config
     * @return void
     */
    private function parseProcessors(array $config)
    {
        $container = $this->container;
        foreach ($config as $key => $processor) {

            $this->container['postman.processor.'.$key] = $this->container->share(
                function() use ($processor, $container) {
                    return new $processor($container);
                }
            );

            $this->container['event_dispatcher']->addListener(
                'postman.call_processors',
                array($this->container['postman.processor.'.$key], 'process')
            );

        }
    }

    /**
     * Parse and set-up request filters
     *
     * @param array $config
     * @return void
     */
    private function parseFilters(array $config)
    {
        $container = $this->container;
        foreach ($config as $key => $filter) {

            $this->container['postman.filter.'.$key] = $this->container->share(
                function() use ($filter, $container) {
                    return new $filter($container);
                }
            );

            $this->container['event_dispatcher']->addListener(
                'postman.filter_request',
                array($this->container['postman.filter.'.$key], 'listenFilterEvent')
            );

        }
    }
}
