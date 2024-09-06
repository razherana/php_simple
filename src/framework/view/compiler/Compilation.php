<?php

namespace framework\view\compiler;

use framework\view\View;

class Compilation
{
  /**
   * Contains the compiler for the view
   * @var AbstractCompiler $compiler
   */
  protected $compiler;

  /**
   * @param AbstractCompiler $compiler
   */
  public function __construct($compiler, $load_view = true)
  {
    $this->compiler = $compiler;

    if ($load_view)
      $this->load_view();
  }

  protected function load_view()
  {
    $this->compiler->compile_and_save_content();

    $view_var_class = ($this->compiler->get_view_var_class());

    // Sets the view var
    $view_var = $this->compiler->view_element->vars = new $view_var_class($this->compiler->view_element->vars);

    // Saves the view var to the View
    View::$view_vars[$this->compiler->view_element->view_name] = $view_var;
  }
}
