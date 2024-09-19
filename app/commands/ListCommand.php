<?php

namespace commands;

use framework\components\console\Console;
use framework\components\console\ConsoleCommand;

class ListCommand extends ConsoleCommand
{
  public function get(): string
  {
    return "list";
  }

  public function execute($args): void
  {
    // Removes the help
    $commands = (new Console(['no_use_data']))->read_cached_config('commands');

    foreach ($commands as $command)
      echo "  - " . (new $command)->get() . "\n";
  }

  public function help(): string
  {
    return "List all the commands";
  }
}
