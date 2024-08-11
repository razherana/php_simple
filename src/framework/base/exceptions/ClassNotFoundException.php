<?php

namespace framework\base\exceptions;

/**
 * Occurs when a class is not found by the autoloader
 */
class ClassNotFoundException extends DefaultException
{
  /**
   * @param string $className
   */
  public function __construct($className)
  {
    parent::__construct("The class '$className' is not found");
  }
}
