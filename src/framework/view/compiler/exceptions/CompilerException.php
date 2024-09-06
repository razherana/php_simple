<?php

namespace framework\view\compiler\exceptions;

use framework\base\exceptions\DefaultException;
use framework\view\compiler\AbstractCompiler;

/**
 * Default exception for a compiler
 */
class CompilerException extends DefaultException
{
  /** @var ?AbstractCompiler $compiler */
  public $compiler = null;

  public function __construct($message = "", $compiler = null)
  {
    $this->compiler = $compiler;
    parent::__construct($message);
  }
}
