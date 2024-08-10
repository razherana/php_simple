<?php

namespace vendor\main\util;

class Config
{

  /**
   * Get the value of a config var
   */
  public static function get($config_file_without_php, $config_var_name)
  {
    return (include(___DIR___ . "/config/$config_file_without_php.php"))[$config_var_name] ?? null;
  }
}
