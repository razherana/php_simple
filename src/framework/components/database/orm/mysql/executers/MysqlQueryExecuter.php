<?php

namespace framework\components\database\orm\mysql\executers;

use framework\base\config\ConfigurableElement;
use framework\components\database\connection\MySqlConnection;
use mysqli_result;

class MysqlQueryExecuter extends ConfigurableElement
{
  /**
   * Executes a query and returns a mysqli_result or a boolean
   * @param string $q a mysql valid query
   */
  public static function run($q): mysqli_result|bool
  {
    return MySqlConnection::get()->execute_query($q);
  }

  /**
   * Executes a query and return the $callable()
   * Then frees the result
   * @param string $q a mysql valid query
   * @param \Closure $callable A callable binded with the mysqli_result
   */
  public static function do_clean($q, $callable) : mixed
  {
    $res = self::run($q);
    
    $user_result = $callable($res);
    $res->free();

    return $user_result;
  }

  public function config_file(): string
  {
    return 'query_executer/mysql';
  }
}
