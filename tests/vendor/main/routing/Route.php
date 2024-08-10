<?php

namespace vendor\main\routing;

use vendor\main\routing\singleton\RouteSave;
use vendor\main\uri\Url;

class Route
{
  private $uri, $method;
  private $isThisEntered = false;
  private $name = null;
  public $middlewares = [];

  public function save()
  {
    $v = RouteSave::$routeSave;
    if ($v === null) $v = [];
    $v[] = new RouteInfo($this->uri, $this->method, $this->name, new RouteVars($this->uri), $this->middlewares);
    RouteSave::$routeSave = $v;
  }

  public function __construct($uri, $method)
  {
    $this->uri = $uri;
    $this->method = $method;
  }

  public function realPost($callable)
  {
    $method = Url::requestMethod();
    if ($method == 'POST') {
      if (Url::isCurrentUri($this->uri)) {
        $this->isThisEntered = true;
        RouteSave::$routeEntered = new RouteEntered($this->uri, $method, $callable, new RouteVars($this->uri));
      }
    }
    return $this;
  }

  public function realPost2($dos)
  {
    $method = Url::requestMethod();
    if ($method == 'POST') {
      if (Url::isCurrentUri($this->uri)) {
        $this->isThisEntered = true;
        RouteSave::$routeEntered = new RouteEntered($this->uri, $method, $dos, new RouteVars($this->uri));
      }
    }
    return $this;
  }

  public function realGet($callable)
  {
    $method = Url::requestMethod();
    if ($method == 'GET') {
      if (Url::isCurrentUri($this->uri)) {
        $this->isThisEntered = true;
        RouteSave::$routeEntered = new RouteEntered($this->uri, $method, $callable, new RouteVars($this->uri));
      }
    }
    return $this;
  }

  public function realGet2($dos)
  {
    $method = Url::requestMethod();
    if ($method == 'GET') {
      if (Url::isCurrentUri($this->uri)) {
        $this->isThisEntered = true;
        RouteSave::$routeEntered = new RouteEntered($this->uri, $method, $dos, new RouteVars($this->uri));
      }
    }
    return $this;
  }


  public static function get($uri, $callable)
  {
    $a = new static($uri, "GET");
    if (is_array($callable)) {
      return $a->realGet2($callable);
    } else
      return $a->realGet($callable);
  }

  public static function post($uri, $callable)
  {
    $a = new static($uri, "POST");
    if (is_array($callable)) {
      return $a->realPost2($callable);
    } else
      return $a->realPost($callable);
  }

  public function middleware(...$middlewares)
  {
    $this->middlewares = $middlewares;
    
    if(RouteSave::$routeEntered !== null && $this->isThisEntered) {
      RouteSave::$routeEntered->middlewares = $middlewares;
    }

    return $this;
  }

  public function name($name)
  {
    $this->name = $name;
    return $this;
  }

  public function end()
  {
    $config = include(___DIR___ . '/config/route.php');

    if ($config['reload_route']) {
      $v = RouteSave::$routeSave;

      if ($v === null) $v = [];

      $v[] = new RouteInfo($this->uri, $this->method, $this->name, new RouteVars($this->uri), $this->middlewares);

      RouteSave::$routeSave = $v;
    }
  }
}
