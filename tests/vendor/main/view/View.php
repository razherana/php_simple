<?php

namespace vendor\main\view;

use vendor\main\view\Herine\Compiler;

class View
{
  private static $dir = ___DIR___ . "/resources";

  public static function from($name, $datas = [])
  {
    $filepath = self::$dir . "/" . $name . ".php";
    new ViewData($datas);
    if (file_exists($filepath)) {
      include $filepath;
    }
  }

  public static function herine($name, $datas = [])
  {
    if (!file_exists(View::$dir . '/' . $name . '.h.php'))
      throw new \Exception("The view herine file doesn't exist, verify the name", 1);
    $configs = (require(___DIR___ . '/config/view.php'));
    if ($configs['always_compile']) {
      Compiler::compile_and_save($name);
    }
    $filepath = Compiler::COMPILED_FILE_DIR . "/" . str_replace('/', '_', $name) . ".php";
    new ViewData($datas);
    if (file_exists($filepath)) {
      include $filepath;
    } else {
      throw new \Exception("The compiled view file doesn't exist, try to compile in <b><i>php console.php</i></b> first", 1);
    }
  }
}
