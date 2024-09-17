<?php

namespace http\middlewares;

use framework\http\handler\BaseMiddleware;
use framework\http\Response;

class AuthMiddleware extends BaseMiddleware
{
  protected $name_auth = '';
  protected $route_name = '';

  public function __construct($request, $name_auth, $route)
  {
    parent::__construct($request);
    $this->name_auth = $name_auth;
    $this->route_name = $route;
  }

  public function checks(): bool
  {
    return !auth($this->name_auth)->loggedin();
  }

  public function execute(): void
  {
    Response::redirect(route($this->route_name));
  }
}
