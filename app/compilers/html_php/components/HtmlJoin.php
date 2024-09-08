<?php

namespace compilers\html_php\components;

use framework\view\compiler\components\Component;

class HtmlJoin extends Component
{
  public function compile_all($uncompiled_content): string
  {
    // Checks if this view has a htmljoin component
    $mode = "";
    $uncompiled = $this->get_uncompiled_syntax();
    if (!preg_match("/" . $this->get_uncompiled_syntax_regex($uncompiled, $mode) . "/$mode", $uncompiled_content)) {
      return $uncompiled_content;
    }

    // The value to insert
    $join = '$___vars___->use_join(); ?>';

    /* number of <?php tags */
    $open = preg_match_all("/(\<\?php)/", $uncompiled_content);
    /* number of ?> tags */
    $closed = preg_match_all("/(\?\>)/", $uncompiled_content);

    // Append the value
    $uncompiled_content .=
      // If open > closed, there is an open php tag without a closing one so we add a closing tag
      ($open > $closed ? " ?>" : "")
      . "<?php $join";

    return parent::compile_all($uncompiled_content);
  }

  protected function get_uncompiled_syntax(): string
  {
    return "<join @ vars=\"@\" />";
  }

  protected function get_uncompiled_syntax_regex($uncompiled_syntax, &$mode): string
  {
    return ("\<join\s+(\S+)(?:\s+vars=\"(.*?)\"\s+)?\/\>");
  }

  protected function get_compiled_syntax($vars): string
  {
    $name = trim($vars[0], "\"'");
    $variables = trim($vars[1] ?? "[]", " ");
    return "<?php \$___vars___->join('$name', $variables); ?>";
  }
}
