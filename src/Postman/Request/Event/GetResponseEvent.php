<?php

namespace Postman\Request\Event;

use \Symfony\Component\EventDispatcher\Event;
use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;

class GetResponseEvent extends Event
{
  /**
   * @var Request
   */
  protected $request;

  /** @var Response */
  protected $response;

  function __construct(Request $request)
  {
    $this->request = $request;
  }

  /**
   * @param \Postman\Request\Event\Request $request
   */
  public function setRequest($request)
  {
    $this->request = $request;
  }

  /**
   * @return \Postman\Request\Event\Request
   */
  public function getRequest()
  {
    return $this->request;
  }

  /**
   * @param \Postman\Request\Event\Response $response
   */
  public function setResponse($response)
  {
    $this->response = $response;
    $this->stopPropagation();
  }

  /**
   * @return \Postman\Request\Event\Response
   */
  public function getResponse()
  {
    return $this->response;
  }
}