<?php

namespace http\middlewares;

use framework\http\handler\BaseMiddleware;

class template_middleware_name extends BaseMiddleware
{
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
