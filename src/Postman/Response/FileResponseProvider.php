<?php

namespace Postman\Response;

use \Postman\Request\Event\GetResponseEvent;
use \Postman\Container\ContainerAware;
use \Symfony\Component\HttpFoundation\Response;

class FileResponseProvider extends ContainerAware implements ResponseProviderInterface
{
  protected static $mimeTypes = array(
    'css' => 'text/css'
  );

  public function handle(GetResponseEvent $event)
  {
    /** @var $request \Symfony\Component\HttpFoundation\Request */
    $request = $event->getRequest();

    $parametersPos = strpos($request->getRequestUri(), '?');
    $filename = false === $parametersPos ? $request->getRequestUri() : substr($request->getRequestUri(), '0', $parametersPos);

    if (is_file($filename = ($this->get('config.basedir').$filename))) {
      $this->get('logger')->info('File found. URI: '.$request->getRequestUri());

      $response = new Response(file_get_contents($filename));

      $response->headers->set('content-type', $this->guessFileType($filename));
      
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
