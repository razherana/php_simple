<?php

namespace framework\base\exceptions;

use Exception;

class DefaultException extends Exception
{
  /**
   * @param string $message
   */
  public function __construct($message)
  {
    parent::__construct($message, 1);
  }
}
