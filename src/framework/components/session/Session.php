<?php

namespace framework\components\session;

use framework\base\Component;
use framework\base\config\ConfigurableElement;
use framework\components\session\drivers\SessionFileDriver;
use framework\components\session\drivers\SessionMysqlDriver;
use framework\components\session\exceptions\SessionException;
use framework\components\session\interfaces\SessionInitializeInterface;
use framework\components\session\interfaces\SessionReservedKeywordsInterface;

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

    $this->add_reserved_keywords_session();
    $this->initialize_session();
  }

  public function execute() {}

  /**
   * Initiliaze the session in config
   */
  protected function initialize_session()
  {
    $to_initialize = $this->read_cached_config('initialize_session');

    foreach ($to_initialize as $clazz) {

      if (!is_subclass_of($clazz, SessionInitializeInterface::class))
        throw new SessionException("This class doesn't implement the interface : '" . SessionInitializeInterface::class . "'");

      /** @var SessionInitializeInterface $clazz  */
      $clazz::initialize_session();
    }
  }

  /**
   * Add the reserved keywords of session
   */
  protected function add_reserved_keywords_session()
  {
    // Class with reserved keywords
    $classes_for_reserved = $this->read_cached_config('reserved_keywords');

    // Class to add the reserved keywords
    $add_reserved_keywords = $this->read_cached_config('add_reserved_keywords');

    $keywords = [];

    foreach ($classes_for_reserved as $clazz) {

      if (!is_subclass_of($clazz, SessionReservedKeywordsInterface::class))
        throw new SessionException("This class doesn't implement the interface : '" . SessionReservedKeywordsInterface::class . "'");

      /** @var SessionReservedKeywordsInterface $clazz  */
      $keywords = array_merge($keywords, $clazz::get_session_reserved_keywords());
    }

    foreach ($add_reserved_keywords as $class => $var) {
      // Merge the array
      $class::$$var = array_merge($class::$$var, $keywords);
    }
  }
}
