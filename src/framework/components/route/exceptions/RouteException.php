<?php

namespace framework\components\route\exceptions;

use Exception;

/**
 * Default exception for the router and route definition
 */
class RouteException extends Exception
{
  /**
   * @param string $message
   */
  public function __construct($message)
  {
    parent::__construct($message, 1);
  }
}
