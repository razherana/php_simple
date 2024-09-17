<?php

namespace framework\http\handler;

use framework\http\Request;

/**
 * The base class for a middleware
 */
abstract class BaseMiddleware
{
  public $request;

  /** @param Request $request */
  public function __construct($request)
  {
    $this->request = $request;
  }

  /**
   * Checks if the middleware should be executed
   * @return bool 
   */
  abstract public function checks(): bool;

  /**
   * Executes the middleware
   */
  abstract public function execute(): void;
}
