<?php

use compilers\php\PhpCompiler;
use framework\view\comm\ViewElement;
use framework\view\comm\ViewVars;
use framework\view\compiler\Compilation;

/**
 * This function is a fast helper to get a view element with it's content
 * @param string $view_name The view's name
 * @param string $compiler The compiler to use
 * @param array $elements Elements to use (block, templates, ...)
 * @return ViewElement Returns the compiled ViewElement
 */
function view($view_name, $data = [], $compiler = PhpCompiler::class, $elements = [])
{
  // Creates the view element
  $view_el = new ViewElement($view_name);
  $view_el->vars = new ViewVars($data, $elements);

  // Creates the compiler
  $compiler = new $compiler($view_el);

  // Do compilation things
  new Compilation($compiler);

  // Returns the compiled view_element
  return $view_el;
}
