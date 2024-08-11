<?php

namespace framework\base\config;

use framework\base\config\exceptions\ConfigReadingException;
use framework\base\config\exceptions\UnknownConfigException;

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
    $full_path = ___DIR___ . self::CONFIG_DIRECTORY . $file_name . '.php';

    if (!file_exists($full_path)) {
      throw new ConfigReadingException("The file '$file_name' with a full path of '$full_path' doesn't exist");
    }

    $content = (include($full_path));

    if (!isset($content[$config_name])) {
      throw new UnknownConfigException($config_name, $file_name, $full_path);
    }

    return $content[$config_name];
  }

  /**
   * @param string $file_name
   */
  public static function get_all($file_name): array
  {
    $full_path = ___DIR___ . self::CONFIG_DIRECTORY . $file_name . '.php';

    if (!file_exists($full_path)) {
      throw new ConfigReadingException("The file '$file_name' with a full path of '$full_path' doesn't exist");
    }

    return (include($full_path));
  }
}
