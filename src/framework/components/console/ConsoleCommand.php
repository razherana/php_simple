<?php

namespace framework\components\console;

use framework\components\console\exceptions\ConsoleExecutionException;

/**
 * Abstract class defining a console command
 */
abstract class ConsoleCommand
{
  /**
   * Returns the command in string
   * @return string The command
   */
  abstract public function get(): string;

  /**
   * Checks if the command typed is this command
   * @param string[] $args
   */
  public function check($args): bool
  {
    if (empty($args) || !is_array($args))
      throw new ConsoleExecutionException("The args given is empty or is not an array");

    return $args[array_keys($args)[0]] == $this->get();
  }

  /**
   * Executes the command
   * @param $args
   */
  abstract public function execute($args): void;

  /**
   * Get the help message for the command
   * @return string
   */
  abstract public function help(): string;
}
