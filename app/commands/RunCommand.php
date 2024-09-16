<?php

namespace commands;

use framework\components\console\ConsoleCommand;
use framework\components\database\Database;
use framework\components\database\orm\mysql\executers\MysqlQueryExecuter;
use framework\components\route\Router;

class RunCommand extends ConsoleCommand
{
  public function get(): string
  {
    return "run";
  }

  public function execute($args): void
  {
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
    (new Router(true))->initialize();
  }

  protected function run_database()
  {
    $imports = (new Database)->read_config('imports');

    foreach ($imports as $import) {
      $file_content = file_get_contents(___DIR___ . "/database/$import");

      foreach (explode(';', $file_content) as $code)
        MysqlQueryExecuter::run(
          // We use the sql script
          $code,
        );
    }
  }

  public function help(): string
  {
    return "Runs scripts\nAvailable parameters : \n\t-d database's script\n\t-r rebuild routes";
  }
}
