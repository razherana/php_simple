<?php

namespace commands;

use framework\base\config\ConfigReader;
use framework\components\console\Console;
use framework\components\console\ConsoleCommand;

class HelpCommand extends ConsoleCommand
{
  public function get(): string
  {
    return "help";
  }

  /**
   * @return ConsoleCommand
   */
  protected function search_command($command)
  {
    foreach ((new Console(['random_value']))->read_config('commands') as $comm) {
      if (($c = new $comm)->check([$command]))
        return $c;
    }
    echo "The command '" . $command . "' doesn't exist";
  }

  public function execute($args): void
  {
    // Removes the help
    $args = array_slice($args, 1);

    if (count($args) >= 1) {
      echo " ---- \n";
      echo $this->search_command($args[0])->help();
      echo "\n ---- \n";
    } else {
      echo " ---- \n";
      echo "This needs the command as an argument\n";
      echo "help !command_name!";
      echo "\n ---- \n";
    }
  }

  public function help(): string
  {
    return "Get the help description of a command";
  }
}
