<?php

namespace vendor\main\database\connection;

class Database
{
  private static $connection = null;

  public function __wakeup()
  {
  }

  public function __clone()
  {
  }

  private function __construct()
  {
  }

  public static function get()
  {
    if (self::$connection === null) {
      self::$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
      mysqli_set_charset(self::$connection, 'utf8');
    }
    return self::$connection;
  }
}
