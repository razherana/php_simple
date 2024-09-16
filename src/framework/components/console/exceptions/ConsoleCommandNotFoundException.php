<?php

namespace framework\components\console\exceptions;

class ConsoleCommandNotFoundException extends ConsoleExecutionException
{

  public function __construct($command)
  {
    parent::__construct("Command not found : '$command'");
  }
}
