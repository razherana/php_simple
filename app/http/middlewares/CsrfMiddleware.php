<?php

namespace http\middlewares;

use framework\http\Csrf;
use framework\http\handler\BaseMiddleware;
use framework\http\Response;

class CsrfMiddleware extends BaseMiddleware
{
  public static function get_csrf()
  {
    return Csrf::$csrf;
  }

  public function checks(): bool
  {
    return $this->request->method() === "POST" &&
      (($this->request->postParameters[(new Csrf)->read_config('session_keyword')] ?? false) != $this::get_csrf());
  }

  public function execute(): void
  {
    Response::abort(501);
  }
}
