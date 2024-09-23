<?php

namespace framework\components\storage\exceptions;

class UnexistantFileException extends FileException
{

  /**
   * @param string $file_name
   */
  public function __construct($file_name)
  {
    parent::__construct("The file/folder : '$file_name' doesn't exist");
  }
}
