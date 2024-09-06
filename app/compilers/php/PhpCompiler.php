<?php

namespace compilers\php;

use framework\view\comm\ViewVars;
use framework\view\compiler\AbstractCompiler;

class PhpCompiler extends AbstractCompiler
{
  protected function get_compiler_name(): string
  {
    return "php";
  }

  protected function get_extensions(): array
  {
    return ["php"];
  }

  protected function get_components(): array
  {
    return [];
  }

  public function get_view_var_class(): string
  {
    return ViewVars::class;
  }
}
