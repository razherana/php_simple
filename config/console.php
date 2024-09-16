<?php

/**
 * Contains all configs for console
 */

use commands\CacheCommand;
use commands\ExitCommand;
use commands\HelpCommand;

return [
  /**
   * Contains the prefix in each line in console
   */
  "each_line_prefix" => "~~ php_simple_console => ",

  /**
   * Contains commands of Console
   * @var string[]
   */
  "commands" => [
    ExitCommand::class,
    HelpCommand::class,
    CacheCommand::class,
  ],
];
