<?php

namespace vendor\main\routing\singleton;

use vendor\main\routing\RouteEntered;
use vendor\main\routing\RouteInfo;

class RouteSave
{
  public static $routeEntered = null;

  /**
   * @var ?RouteInfo[] $routeSave
   */
  public static $routeSave = null;

  private function __construct()
  {
  }

  public function __clone()
  {
  }

  public function __wakeup()
  {
  }
}
