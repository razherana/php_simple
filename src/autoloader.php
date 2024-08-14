<?php

use framework\base\exceptions\ClassNotFoundException;

spl_autoload_register(function ($className) {
  $classPath = str_replace('\\', '/', $className);
  if (file_exists($fileName = ___DIR___ . '/src/' . $classPath . ".php")) {
    return require_once($fileName);
  }
  throw new ClassNotFoundException($className);
});
