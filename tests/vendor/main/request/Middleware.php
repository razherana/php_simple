<?php

namespace vendor\main\request;

class Middleware
{
  public static $middlewares = null;
  public static function getFromAlias($alias)
  {
    if (self::$middlewares === null)
      self::$middlewares = include(___DIR___ . '/config/middleware.php');
    if (!isset(self::$middlewares[$alias])) {
      throw new \Exception("This middleware doesn't exist : `$alias`", 1);
    }
    return self::$middlewares[$alias];
  }

  /**
   * @param string[] $aliases
   */
  public static function translateAliases($aliases)
  {
    $new_array = [];
    foreach($aliases as $alias) {
      $new_array[] = self::getFromAlias($alias);
    }
    return $new_array;
  }
}
