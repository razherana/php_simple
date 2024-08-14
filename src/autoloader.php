<?php

use framework\base\exceptions\ClassNotFoundException;

spl_autoload_register(function ($className) {
  $classPath = str_replace('\\', '/', $className);

  $sources = include(___DIR___ . '/config/autoloader.php');

  $sources = $sources['sources'] ?? [];

  foreach ($sources as $source)
    if (file_exists($fileName = ___DIR___ . "/$source/" . $classPath . ".php")) {
      return require_once($fileName);
    }

  throw new ClassNotFoundException($className);
});
