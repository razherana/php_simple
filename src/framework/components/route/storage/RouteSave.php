<?php

namespace framework\components\route\storage;

use framework\components\route\Route;

class RouteSave
{
  private function __construct()
  {
  }

  /**
   * Contains all the routes saved
   * @var Route[] $all
   */
  public static $all = [];

  /**
   * Contains the route entered
   * @var ?Route $entered
   */
  public static $entered = null;
}
