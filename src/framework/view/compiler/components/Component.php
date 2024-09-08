<?php

namespace framework\view\compiler\components;

use compilers\html_php\components\HtmlTemplate;
use compilers\star_php\components\StarBlock;
use compilers\star_php\components\StarEndBlock;
use Exception;

/**
 * Represents a block of uncompiled code
 */
abstract class Component
{
  /**
   * Gets the uncompiled syntax
   * i.e: syntax @ other_syntax
   */
  abstract protected function get_uncompiled_syntax(): string;

  /**
   * Gets the compiled syntax with vars
   * i.e: php_syntax @ other_php_syntax
   * 
   * @param array $vars The variables to use in the compiled syntax 
   */
  abstract protected function get_compiled_syntax($vars): string;

  /**
   * Get the regex format of the uncompiled syntax
   * @return string The uncompiled syntax
   */
  protected function get_uncompiled_syntax_regex($uncompiled_syntax, &$mode): string
  {
    // Uses the preg_quote if not overrided
    // Replace $ with a regex expression -> (.*) 
    return str_replace('@', '(\S+)', preg_quote($uncompiled_syntax));
  }

  /**
   * @param string $uncompiled_content
   */
  public function compile_all($uncompiled_content): string
  {
    $uncompiled = $this->get_uncompiled_syntax();

    $mode = "";

    $uncompiled_regex = $this->get_uncompiled_syntax_regex($uncompiled, $mode);

    // Initialize all of the datas
    $offset = 0;
    $match = [];
    $result = preg_match("/$uncompiled_regex/" . $mode, $uncompiled_content, $match, PREG_OFFSET_CAPTURE, $offset);

    while ($result) {
      // Saves the start of the syntax
      $offset_start = $match[0][1];

      // Sets the vars
      $vars = $match;

      // Removes the first element
      unset($vars[0]);

      // Do this because it is offset capture and we remove the offset from the table
      array_walk($vars, function (&$e) {
        $e = $e[0];
      });

      // Repair the keys
      $vars = array_values($vars);

      // Get the new syntax
      $new_syntax = $this->get_compiled_syntax($vars);

      // Replace the old syntax with the new one
      $uncompiled_content = substr_replace($uncompiled_content, $new_syntax, $offset_start, strlen($match[0][0]));

      // Sets the new offset to be position_of_old + strlen of new syntax
      $offset = $offset_start + strlen($new_syntax);

      // Redo the search
      $result = preg_match("/$uncompiled_regex/" . $mode, $uncompiled_content, $match, PREG_OFFSET_CAPTURE, $offset);
    }

    return $uncompiled_content;
  }
}
