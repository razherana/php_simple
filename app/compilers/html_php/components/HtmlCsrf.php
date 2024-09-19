<?php

namespace compilers\html_php\components;

use framework\http\Csrf;
use framework\view\compiler\components\Component;

class HtmlCsrf extends Component
{
  protected function get_compiled_syntax($vars): string
  {
    return '<input type="hidden" name="' . (new Csrf)->read_config('session_keyword') . '" value="<?php echo framework\http\Csrf::$csrf ?>">';
  }

  protected function get_uncompiled_syntax_regex($uncompiled_syntax, &$mode): string
  {
    return '\\<csrf\\W*\\/?\\>';
  }

  protected function get_uncompiled_syntax(): string
  {
    return "<csrf/>";
  }
}
