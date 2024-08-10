<?php

function ___autoloader_function___(string $className)
{
  $classPath = str_replace('\\', '/', $className);
  if (file_exists($fileName = ___DIR___ . '/src/' . $classPath . ".php")) {
    return require_once($fileName);
  }
  throw new \Exception("Class Not Found", 1);
}

spl_autoload_register('___autoloader_function___');
