<?php

namespace Postman\Request\Filter;

use \Symfony\Component\HttpFoundation\Request;
use \Postman\Configuration\Event\LoadConfigurationEvent;
use \Postman\Container\Container;

class UriFilter extends Filter
{
    protected $config;

    protected $rules;

    protected static $defaultConfig = array(
        'skip_real_files' => true,
        'directory_index' => 'index.html',
        'rules'           => array()
    );

    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->get('event_dispatcher')->addListener(
            'postman.load_configuration.uri_filter',
            array($this, 'listenToLoadConfiguration')
        );
    }


    public function filter(Request $request)
    {
        $urlParts = parse_url($request->getRequestUri());

        $this->get('logger')->debug('Filtering '.$request->getUri());

        $path = rtrim($this->getParameter('basedir').$urlParts['path'], '/');

        foreach ($this->rules as $rule) {
            $pattern = sprintf('#%s#', $rule['pattern']);
            if (1 === preg_match($pattern, $urlParts['path'])) {
                $request->server->set(
                    'PATH_TRANSLATED',
                    preg_replace($pattern, 'redirect:'.$rule['destination'], $urlParts['path'])
                );

                break;
            }
        }

        if ($this->config['skip_real_files']) {
            while (strlen($path) >= strlen($this->getParameter('basedir'))) {
                if (is_file($path)) {
                    $request->server->set('SCRIPT_FILENAME', $path);

                    return;
                } else if (is_dir($path) && is_file($path.'/'.$this->config['directory_index'])) {
                    $request->server->set('SCRIPT_FILENAME', $path.'/'.$this->config['directory_index']);

                    return;
                } else if (is_dir($path)) {
                    return;
                }

                $path = rtrim(substr($path, 0, strrpos($path, '/')), '/');
            }
        }
    }

    public function listenToLoadConfiguration(LoadConfigurationEvent $event)
    {
        $config = array_merge(self::$defaultConfig, $event->getConfig());

        $this->rules  = $config['rules'];
        unset($config['rules']);
        $this->config = $config;
    }
}
