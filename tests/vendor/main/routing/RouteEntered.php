<?php

namespace vendor\main\routing;

use vendor\main\request\Middleware;
use vendor\main\uri\Url;

class RouteEntered extends RouteInfo
{
  private $callable = null;
  private $callable2 = null;

  public function __construct($uri, $method, $callable, $routeVars = null)
  {
    $this->uri = $uri;
    $this->method = $method;

    if (is_array($callable))
      $this->callable = $callable;
    else
      $this->callable2 = $callable;

    $this->routeVars = $routeVars;
  }

  public function callMethod()
  {
    $this->callMiddlewares();

    if ($this->routeVars === null) $vars = [];
    else $vars = $this->routeVars->getVarsOfUri(Url::getUri());

    if ($this->callable !== null) {
      $callable = $this->callable;
      return (new $callable[0]())->{$callable[1]}(...$vars);
    } else {
      $callable = $this->callable2->invoke();
      return $callable(...$vars);
    }
  }

  private function callMiddlewares()
  {
    $this->middlewares = array_merge($this->middlewares, (include (___DIR___ . '/config/middleware.php'))['autorunned_middlewares']);
    $this->middlewares = array_merge(array_unique($this->middlewares));
    
    foreach (Middleware::translateAliases($this->middlewares) as $middleware) {
      if ($middleware::run() === false) {
        return $middleware::on_error();
      }
    }
  }
}
