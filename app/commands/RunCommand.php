<?php

namespace commands;

use framework\components\console\ConsoleCommand;
use framework\components\database\Database;
use framework\components\database\orm\mysql\executers\MysqlQueryExecuter;
use framework\components\route\Router;
use framework\components\session\Session;

class RunCommand extends ConsoleCommand
{
  public function get(): string
  {
    return "run";
  }

  public function check_init_db()
  {
    global $db;

    if (is_a($db, Database::class)) return;

    $db = new Database;
    $db->initialize();
    $db->execute();
  }

  public function execute($args): void
  {
    $this->check_init_db();

    global $db;
    /** @var Database $db */

    $args = array_slice($args, 1);

    $params = array_filter($args, function ($e) {
      return preg_match("/\\-\\w/", $e);
    });

    foreach ($params as $arg)
      switch ($arg) {
        case "-d":
          $this->run_database();
          break;
        case "-r":
          $this->run_routes();
          break;
      }
  }

  protected function run_routes()
  {
    (new CacheCommand)->clear_route();
    (new Router)->initialize();
  }

  protected function run_database()
  {
    global $db;
    $imports = $db->read_cached_config('imports');

    foreach ($imports as $import) {
      $file_content = file_get_contents(___DIR___ . "/database/$import");

      foreach (explode(';', $file_content) as $code)
        // We check if the query is empty
        if (trim(trim($code), ' ') != "")
          MysqlQueryExecuter::run(
            // We use the sql script
            $code,
          );
    }

    // Run the session query
    $session = (new Session);
    $query = $session->read_cached_config('mysql_structure');
    $query = str_replace('<<session>>', $session->read_cached_config('mysql_table'), $query);

    MysqlQueryExecuter::run(
      // We use the sql script
      $query,
    );
  }

  public function help(): string
  {
    return "Runs scripts\nAvailable parameters : \n\t-d database's script\n\t-r rebuild routes";
  }
}
