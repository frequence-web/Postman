<?php
/**
 * The Request Class do some things...
 *
 * @author Yohan Giarelli <yohan@giarelli.org>
 */

namespace Postman\Request;

use \Symfony\Component\HttpFoundation\Request as BaseRequest;

class Request extends BaseRequest
{
  public static function createFromHttp(array $httpRequest, $port = 80)
  {
    $matches = array();
    preg_match('#(?<method>GET|POST|PUT|DELETE) (?<path>/.*) (?<protocol>HTTP/1.(0|1))#', $httpRequest[0], $matches);
    
    $server = array(
      'SERVER_PROTOCOL' => $matches['protocol']
    );

    $request = self::create($matches['path'], $matches['method'], array(), array(), array(), $server);

    $request->server->set('SERVER_PORT', $port);

    foreach ($httpRequest as $header) {
      $match = null;
      if (preg_match('#(?<key>[-a-zA-Z]+): (?<value>.*)#', $header, $match)) {
        $request->headers->set($match['key'], $match['value']);
      }
    }

    foreach ($request->server->all() as $key => $value)
    {
      $headerKey = strpos($key, 'HTTP_') === 0 ? substr($key, 5) : $key;
      if ($request->headers->has($headerKey)) {
        $request->server->set($key, $request->headers->get($headerKey));
      }
    }

    return $request;
  }
}
