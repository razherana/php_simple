<?php

namespace framework\components\console;

use commands\ListCommand;
use framework\base\Component;
use framework\base\config\ConfigurableElement;
use framework\components\console\exceptions\ConsoleCommandNotFoundException;
use framework\components\console\exceptions\ConsoleExecutionException;
use framework\components\console\exceptions\ConsoleInitializationException;

class Console extends ConfigurableElement implements Component
{
  /**
   * Tells if in readline mode
   * @var bool $readline
   */
  protected $readline = false;

  /**
   * The args of the command
   * @var string[]
   */
  protected $args = [];

  /**
   * Commands to use
   * @var string[] $commands
   */
  protected $commands = [];

  /**
   * @param $argv The global argv var
   */
  public function __construct($argv)
  {
    if (count($argv) <= 0)
      throw new ConsoleInitializationException("This shouldn't happen, the argv is empty");
    elseif (count($argv) >= 1)
      $argv = array_slice($argv, 1);

    // Then add the value
    $this->args = $argv;
  }

  public function config_file(): string
  {
    return 'console';
  }

  public function initialize()
  {
    if (count($this->args) == 0) {

      if (!extension_loaded("readline"))
        throw new ConsoleInitializationException("php readline extension is not loaded, please load the extension before using Console");

      $this->readline = true;
    }

    $this->commands = $this->read_cached_config('commands');
  }

  public function execute()
  {

    if (!$this->readline) {
      foreach ($this->commands as $command) {
        /** @var ConsoleCommand $comm */
        if (($comm = new $command)->check($this->args))
          return $comm->execute($this->args);
      }
      throw new ConsoleExecutionException("No commands found for this");
    } else while (true) {
      // Read line
      $commands = readline($this->read_cached_config('each_line_prefix'));

      // If the command is newline
      if ($commands === false)
        continue;

      $commands = explode(' ', $commands);

      // Initialize to false
      $found = false;

      foreach ($this->commands as $command) if (($comm = new $command)->check($commands)) {
        /** @var ConsoleCommand $comm */
        // Execute command
        $comm->execute($commands);

        // Sets to true and break
        $found = true;
        break;
      }

      if ($found === false) {
        (new ListCommand)->execute([]);
      }
    }
  }
}
