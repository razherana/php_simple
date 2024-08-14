<?php

use framework\base\exceptions\ClassNotFoundException;

spl_autoload_register(function ($className) {
  $classPath = str_replace('\\', '/', $className);

  $sources = include(___DIR___ . '/config/autoloader.php');

  if (!$sources) {
    throw new Exception("The config for the autoloader can't be loaded");
  }

  $sources = $sources['sources'] ?? [];

  foreach ($sources as $source)
    if (file_exists($fileName = ___DIR___ . "/$source/" . $classPath . ".php")) {
      return require_once($fileName);
    }

  // if we not check, this will do an infinite call to the autoloader. 
  if ($className === ClassNotFoundException::class)
    throw new Exception("The autoloader doesn't work OR the class : '$className' doesn't exist", 1);
  else
    throw new ClassNotFoundException($className);
});
