<?php

namespace framework\view;

use framework\view\comm\ViewVars;
use framework\base\config\ConfigurableElement;

class View extends ConfigurableElement
{
  /**
   * Contains all ViewVars instanciated with the key as the view's name
   * @var array<string, ViewVars> $view_vars
   */
  public static $view_vars = [];

  public function config_file(): string
  {
    return 'view';
  }
}
