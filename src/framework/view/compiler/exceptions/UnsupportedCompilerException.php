<?php

namespace framework\view\compiler\exceptions;

class UnsupportedCompilerException extends CompilerException
{
  public function __construct($compiler = null)
  {
    parent::__construct("This compiler is unsupported for this operation : " . $compiler::class, $compiler);
  }
}
