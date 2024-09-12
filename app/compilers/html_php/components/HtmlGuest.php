<?php

namespace compilers\html_php\components;

use framework\view\compiler\components\Component;
use framework\view\compiler\exceptions\CompilerException;

class HtmlGuest extends Component
{
  protected function get_compiled_syntax($vars): string
  {
    if (empty($vars))
      throw new CompilerException("This shouldn't happen");

    // We first trim whitespace then after we trim the "" or '' to not remove the spaces inside the quotes
    return '<?php if(!auth("' . $vars[0] . '")->loggedin()) : ?>';
  }

  protected function get_uncompiled_syntax_regex($uncompiled_syntax, &$mode): string
  {
    return '\<guest\s+name\s*\=\s*"(.*?)"\s*\>';
  }

  protected function get_uncompiled_syntax(): string
  {
    return "<guest name=\"@\">";
  }
}
