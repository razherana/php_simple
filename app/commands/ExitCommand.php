<?php

namespace commands;

use framework\components\console\ConsoleCommand;

class ExitCommand extends ConsoleCommand
{
  public function get(): string
  {
    return "exit";
  }

  public function execute($args): void
  {
    echo "Exiting right now ...";
    exit;
  }

  public function help(): string
  {
    return "Exit the console application";
  }
}
