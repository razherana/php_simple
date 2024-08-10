<?php

namespace framework\base\config;

class ConfigReader
{
  /**
   * Please take note that this doesn't take the ___DIR___ constant
   */
  public const CONFIG_DIRECTORY = '/config/';

  /**
   * @param string $file_name
   * @param string $config_name
   */
  public static function get($file_name, $config_name)
  {
    return (include(___DIR___ . self::CONFIG_DIRECTORY . $file_name . '.php'))[$config_name];
  }

  /**
   * @param string $file_name
   */
  public static function get_all($file_name): array
  {
    return (include(___DIR___ . self::CONFIG_DIRECTORY . $file_name . '.php'));
  }
}
