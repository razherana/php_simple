<?php

namespace framework\components\database\connection;

use framework\components\database\Database;
use framework\components\database\exceptions\MysqlConnectionException;
use mysqli;

class MySqlConnection extends mysqli
{
  /**
   * Contains the singleton instance of PDO
   * @var static $self
   */
  private static $self = null;

  /**
   * Establish the connection
   * @param Database $database
   */
  public static function initialize($database = null)
  {
    static::$self = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, !is_null($database) ? $database->read_config('port') : null);
    if (static::$self == false)
      throw new MysqlConnectionException("Connection to database failed");
  }

  public static function get()
  {
    if (is_null(static::$self)) {
      throw new MysqlConnectionException("The database connection isn't initialized");
    }
    return static::$self;
  }
}
