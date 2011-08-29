<?php

namespace Postman\Processor\Event;

use \Symfony\Component\EventDispatcher\Event;
use \Postman\Request\Request;
use \Symfony\Component\HttpFoundation\Response;

class CallProcessorEvent extends Event
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * @param \Postman\Request\Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return \Postman\Request\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @return void
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}