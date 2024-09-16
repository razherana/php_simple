<?php

namespace framework\components\route\storage;

use framework\components\route\exceptions\RouteException;
use framework\components\route\Route;

class RouteSave
{
  private function __construct() {}

  /**
   * Contains all the routes saved
   * @var array<string, Route[]> $all
   */
  public static $all = [];

  /**
   * Contains the route entered
   * @var ?Route $entered
   */
  public static $entered = null;

  public static function uri_from_route_name($route_name, $args = [])
  {
    foreach (static::$all as $routes)
      foreach ($routes as $route) if ($route->name === $route_name)
        return $route->generate_uri($args);

    throw new RouteException("The route requested '$route_name' doesn't exist, don't forget to add ->save() at the end of the route creation");
  }
}
