<?php
/**
 * The RequestHandler Class do some things...
 *
 * @author Yohan Giarelli <yohan@giarelli.org>
 */

namespace Postman\Request;

use \Symfony\Component\HttpFoundation\Response;
use \Postman\Request\Event\RequestEvent;
use \Postman\Container\ContainerAware;
use \Postman\Request\Event\GetResponseEvent;

class RequestHandler extends ContainerAware implements RequestHandlerInterface
{
  public function handle(RequestEvent $event)
  {
    $requestHeaders = array();
    while (($line = fgets($event->getConnection(), 1024)) != "\r\n") {
        $requestHeaders[] = $line;
    }

    $request = Request::createFromHttp($requestHeaders, $this->get('config.port'));
    $request->server->set('REMOTE_HOST', stream_socket_get_name($event->getConnection(), true));
    $request->server->set('REMOTE_ADDR', gethostbyname(stream_socket_get_name($event->getConnection(), true)));

    $this->get('logger')->debug('Sending get_response event');
    $getResponseEvent = new GetResponseEvent($request);
    $this->get('event_dispatcher')->dispatch('postman.get_response', $getResponseEvent);

    $this->get('logger')->info('Sending response');
    fwrite($event->getConnection(), (string)$getResponseEvent->getResponse(), strlen((string)$getResponseEvent->getResponse()));
  }
}
