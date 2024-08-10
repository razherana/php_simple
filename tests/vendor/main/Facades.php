<?php

namespace vendor\main;

class Facades
{
  /**
   * Name of the functions to use like facades --
   * "name_of_facade" => "name_of_the_function"
   */
  public static $functions = [];

  public static function __callStatic($name, $arguments)
  {
    if (array_key_exists($name, static::$functions)) {
      return (new static())->{static::$functions[$name]}($arguments);
    } else if (null) {
    }
  }
}
