<?php

namespace framework\components\database;

use framework\base\Component;
use framework\base\config\ConfigurableElement;
use framework\components\database\connection\MySqlConnection;
use framework\components\database\exceptions\DatabaseException;

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
    switch ($type = $this->read_cached_config('type')) {
      case 'mysql':
        MySqlConnection::initialize($this);
        break;
      default:
        throw new DatabaseException("This database type is not supported : $type");
    }
  }

  public function execute() {}
}
