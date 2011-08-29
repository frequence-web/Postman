<?php

namespace Postman\Request\Event;

use \Symfony\Component\EventDispatcher\Event;

class RequestEvent extends Event
{

  protected $connection;


  public function __construct($connection)
  {
    $this->connection = $connection;
  }

  public function setConnection($connection)
  {
    $this->connection = $connection;
  }

  public function getConnection()
  {
    return $this->connection;
  }


}