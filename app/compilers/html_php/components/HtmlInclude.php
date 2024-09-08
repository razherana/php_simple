<?php

namespace compilers\html_php\components;

use framework\view\compiler\components\Component;

class HtmlInclude extends Component
{
  protected function get_uncompiled_syntax(): string
  {
    return "<include @ vars=\"@\" />";
  }

  protected function get_uncompiled_syntax_regex($uncompiled_syntax, &$mode): string
  {
    return ("\<include\s+(\S+)(?:\s+vars=\"(.*?)\"\s+)?\/\>");
  }

  protected function get_compiled_syntax($vars): string
  {
    $name = trim($vars[0], "\"'");
    $variables = trim($vars[1] ?? "[]", " ");
    return "<?php \$___vars___->include_block('$name', $variables); ?>";
  }
}
