<?php

namespace compilers\html_php\components;

use framework\view\compiler\components\Component;

class HtmlEndFor extends Component
{
  protected function get_compiled_syntax($vars): string
  {
    return "<?php } ?>";
  }

  protected function get_uncompiled_syntax_regex($uncompiled_syntax, &$mode): string
  {
    return '\<\s*\/\s*(?:foreach|for)\s*\>';
  }

  protected function get_uncompiled_syntax(): string
  {
    return "</foreach|for>";
  }
}
