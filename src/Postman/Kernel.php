<?php
/**
 * The Kernel Class do some things...
 *
 * @author Yohan Giarelli <yohan@giarelli.org>
 */

namespace Postman;

use \Postman\Request\Event\RequestEvent;
use \Postman\Container\ContainerAware;

class Kernel extends ContainerAware
{

  protected $stream;

  public function boot()
  {
    $this->stream = stream_socket_server(sprintf(
      'tcp://%s:%s',
      $this->get('config.listen'),
      $this->get('config.port')
    ));

    $this->get('logger')->info(sprintf(
      'Started server. Listening on %s:%s',
      $this->get('config.listen'),
      $this->get('config.port')
    ));

    while ($con = stream_socket_accept($this->stream, $this->get('config.timeout'))) {

      $this->get('logger')->info('Connection accepted');

      $this->get('event_dispatcher')->dispatch('postman.request', new RequestEvent($con));
      fclose($con);
    }
  }

}
