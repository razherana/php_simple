<?php

namespace framework\components\database;

use framework\base\Component;
use framework\base\config\ConfigurableElement;
use framework\components\database\connection\MySqlConnection;

class Database extends ConfigurableElement implements Component
{
  public function config_file(): string
  {
    return "database";
  }

  public function initialize()
  {
    /**
     * Connect to database
     */
    if ($this->read_cached_config('type') == 'mysql') {
      MySqlConnection::initialize($this);
    }
  }

  public function execute() {}
}
