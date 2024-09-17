<?php

namespace http\middlewares;

use framework\http\handler\BaseMiddleware;
use framework\http\Request;

class template_middleware_name extends BaseMiddleware
{
  public $request;

  /** @param Request $request */
  public function __construct($request)
  {
    $this->request = $request;
  }

  public function checks(): bool
  {
    // The checking
    return true;
  }

  public function execute(): void
  {
    // Execute response things here
  }
}
