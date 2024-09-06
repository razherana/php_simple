<?php

namespace framework\view\compiler\components;

/**
 * Represents a block of uncompiled code
 */
abstract class Component
{
  /**
   * Gets the uncompiled syntax
   * i.e: syntax $ other_syntax
   */
  abstract protected function get_uncompiled_syntax(): string;

  /**
   * Gets the compiled syntax with vars
   * i.e: php_syntax $ other_php_syntax
   * 
   * @param string $vars The variables to use in the compiled syntax 
   */
  abstract protected function get_compiled_syntax($vars): string;

  /**
   * @param string $uncompiled_content
   */
  final public function compile_all($uncompiled_content): string
  {
    $uncompiled = $this->get_uncompiled_syntax();

    // Replace $ with a regex expression -> (.*) 
    $uncompiled_regex = str_replace('$', '(.*)', $uncompiled, $counts);

    // Initialize all of the datas
    $offset = 0;
    $match = [];
    $result = preg_match("/$uncompiled_regex/", $uncompiled_content, $match, 0, $offset);

    while ($result) {
      // Saves the start of the syntax
      $offset_start = $offset;

      // Sets the vars
      $vars = $match;

      // Removes the first element
      unset($vars[0]);

      // Repair the keys
      $vars = array_values($vars);

      // Get the new syntax
      $new_syntax = $this->get_compiled_syntax($vars);

      // Replace the old syntax with the new one
      $uncompiled_content = substr_replace($uncompiled_content, $new_syntax, $offset_start, strlen($match[0]));

      // Sets the new offset to be + strlen of new syntax
      $offset += strlen($new_syntax);

      // Redo the search
      $result = preg_match("/$uncompiled_regex/", $uncompiled_content, $match, 0, $offset);
    }

    return $uncompiled_content;
  }
}
