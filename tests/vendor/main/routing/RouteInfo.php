<?php

namespace vendor\main\routing;

class RouteInfo
{
  protected $uri, $method, $name;
  
  public $middlewares = [];

  public $routeVars;

  public function __construct($uri, $method, $name, $routeVars, $middlewares = [])
  {
    $this->uri = $uri;
    $this->method = $method;
    $this->name = $name;
    $this->routeVars = $routeVars;
    $this->middlewares = $middlewares;
  }

  public function getUri()
  {
    return $this->uri;
  }

  public function getMethod()
  {
    return $this->method;
  }

  public function getName()
  {
    return $this->name;
  }
}
