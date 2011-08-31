<?php
/**
 * The RequestHandler Class do some things...
 *
 * @author Yohan Giarelli <yohan@giarelli.org>
 */

namespace Postman\Request\Handler;

use \Symfony\Component\HttpFoundation\Response;
use \Postman\Request\Event\RequestEvent;
use \Postman\Container\ContainerAware;
use \Postman\Request\Event\FilterRequestEvent;
use \Postman\Request\Event\GetResponseEvent;
use \Postman\Request\Request;

class Handler extends ContainerAware implements HandlerInterface
{
    public function handle(RequestEvent $event)
    {
        $requestHeaders = array();
        while (($line = fgets($event->getConnection(), 1024)) != "\r\n") {
            $requestHeaders[] = $line;
        }

        $request = Request::createFromHttp($requestHeaders);
        $request->server->set('REMOTE_HOST', stream_socket_get_name($event->getConnection(), true));
        $request->server->set('REMOTE_ADDR', gethostbyname(stream_socket_get_name($event->getConnection(), true)));
        $request->server->set('DOCUMENT_ROOT', $this->getParameter('basedir'));

        $this->get('logger')->debug('Sending filter_request event');
        $filterRequestEvent = new FilterRequestEvent($request);
        $this->get('event_dispatcher')->dispatch('postman.filter_request', $filterRequestEvent);

        $this->get('logger')->debug('Sending get_response event');
        $getResponseEvent = new GetResponseEvent($request);
        $this->get('event_dispatcher')->dispatch('postman.get_response', $getResponseEvent);

        $this->get('logger')->info('Sending response');
        fwrite($event->getConnection(), (string)$getResponseEvent->getResponse(), strlen((string)$getResponseEvent->getResponse()));
    }
}
