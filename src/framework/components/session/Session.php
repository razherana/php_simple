<?php

namespace framework\components\session;

use framework\base\Component;
use framework\base\config\ConfigurableElement;
use framework\components\session\drivers\SessionFileDriver;
use framework\components\session\drivers\SessionMysqlDriver;
use framework\components\session\exceptions\SessionException;

class Session extends ConfigurableElement implements Component
{
  public function config_file(): string
  {
    return "session";
  }

  public function initialize()
  {
    $config = $this->read_cached_config('driver');

    switch ($config) {
      case "file":
        $directory = $this->read_cached_config('file_directory');
        session_save_path(___DIR___ . "/" . trim($directory, " /"));
        $session_handler = new SessionFileDriver(HASH_CODE);
        break;

      case "mysql":
        $session_handler = new SessionMysqlDriver(
          HASH_CODE,
          $this->read_cached_config('mysql_table'),
          $this->read_cached_config('mysql_structure')
        );
        break;

      default:
        throw new SessionException("This driver is not supported : $config");
    }

    session_set_save_handler($session_handler, true);

    session_start();
  }

  public function execute() {}
}
