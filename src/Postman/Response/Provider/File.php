<?php

namespace Postman\Response\Provider;

use \Postman\Request\Event\GetResponseEvent;
use \Postman\Container\ContainerAware;
use \Symfony\Component\HttpFoundation\Response;

class File extends Provider implements ProviderInterface
{
    protected static $mimeTypes = array(
        'css' => 'text/css'
    );

    public function handle(GetResponseEvent $event)
    {
        /** @var $request \Symfony\Component\HttpFoundation\Request */
        $request = $event->getRequest();

        $filename = null;
        if ($request->server->has('SCRIPT_FILENAME')) {
            $filename = $request->server->get('SCRIPT_FILENAME');
            $this->get('logger')->info('Using script filename path : '.$filename);
        } elseif ($request->server->has('PATH_TRANSLATED')) {
            $filename = $this->getParameter('basedir').substr($request->server->get('PATH_TRANSLATED'), 9);
            $this->get('logger')->info('Using translated path : '.$filename);
        } else {
            $urlParts = parse_url($request->getRequestUri());
            $filename = $this->getParameter('basedir').$urlParts['path'];
        }

        if (is_file($filename)) {
            $this->get('logger')->info('File found. URI: '.$request->getRequestUri());

            $response = new Response(file_get_contents($filename));
            $response->headers->set('content-type', $this->guessFileType($filename));

            $this->callProcessors($request, $response);
            $event->setResponse($response);

            $this->get('logger')->info('Delivering '.$this->guessFileType($filename).' file.');
        }

    }

    protected function guessFileType($filename)
    {
        $extension = substr($filename, strrpos($filename, '.') + 1);

        if (in_array($extension, self::$mimeTypes)) {
            return self::$mimeTypes[$extension];
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $type = finfo_file($finfo, $filename);
        finfo_close($finfo);

        return $type;
    }

}
