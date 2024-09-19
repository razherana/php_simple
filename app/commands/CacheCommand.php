<?php

namespace commands;

use framework\components\console\ConsoleCommand;
use framework\components\route\Router;
use framework\view\View;

class CacheCommand extends ConsoleCommand
{
  public function get(): string
  {
    return "ccache";
  }

  public function execute($args): void
  {
    $args = array_slice($args, 1);

    $params = array_filter($args, function ($e) {
      return preg_match("/\\-\\w/", $e);
    });

    foreach ($params as $arg) {
      switch ($arg) {
        case "-r":
          $this->clear_route();
          break;
        case "-v":
          $this->clear_view();
          break;
        case '-a':
          $this->clear_route();
          $this->clear_view();
          break;
      }
    }
  }

  protected function clear_view()
  {
    foreach (
      array_diff(
        scandir($dir = ___DIR___ . '/' . (new View)->read_cached_config('compiled')),
        ['.', '..']
      )
      as $file
    ) {
      unlink($dir . '/' . $file);
    }
  }

  public function clear_route()
  {
    foreach (
      array_diff(
        scandir($dir = ___DIR___ . '/' . (new Router)->read_cached_config('route_storage')),
        ['.', '..']
      )
      as $file
    ) {
      unlink($dir . '/' . $file);
    }
  }

  public function help(): string
  {
    return "Clears cache of this application\nAvailable parameters : \n\t-r routes\n\t-v views\n\t-a all";
  }
}
