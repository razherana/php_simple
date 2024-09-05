<?php

namespace framework\base\config;

use framework\base\config\exceptions\UnknownConfigException;

/**
 * This interface signals that the Component has a config file
 */
abstract class ConfigurableElement
{
  /** 
   * Contains the cached config
   * @var array<string, mixed> $cached_config
   */
  protected static $cached_config = null;

  /**
   * Returns the config file
   */
  abstract public function config_file(): string;

  /**
   * Reads config
   * @param string $config_name
   */
  public function read_config($config_name)
  {
    return ConfigReader::get($this->config_file(), $config_name);
  }

  /**
   * Reads config from cache
   * @param array<string, mixed> $content
   * @param string $config_name
   */
  public function read_cached_config($config_name)
  {
    if (is_null(static::$cached_config)) {
      static::$cached_config = ConfigReader::get_all($this->config_file());
    }

    $content = static::$cached_config;

    if (!isset($content[$config_name])) {
      throw new UnknownConfigException($config_name, $this->config_file(), $this->config_file());
    }

    return $content[$config_name];
  }
}
