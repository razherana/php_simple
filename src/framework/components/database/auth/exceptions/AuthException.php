<?php

namespace framework\components\database\auth\exceptions;

use framework\base\exceptions\DefaultException;

class AuthException extends DefaultException
{
  public $auth;

  public function __construct($message, $auth = null)
  {
    $this->auth = $auth;
    parent::__construct($message);
  }
}
