<?php

namespace framework\base\config;

/**
 * This interface signals that the Component has a config file
 */
abstract class ConfigurableElement
{
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
}
