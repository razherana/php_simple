<?php

require_once('env.php');
require_once(___DIR___ . '/vendor/autoloader.php');
require_once(___DIR___ . '/vendor/main/base_functions.php');

use vendor\main\console\Console;
use vendor\main\routing\singleton\RouteSave;

echo "Commandes : 
  -- create model model_name model_table ?primary_key
  -- create controller controller_name 
  -- create middleware middleware_name 
  -- create singleton singleton_name ?singleton_var_name 
  -- compile views
  -- compile routes
  -- generate auth
";

$user = readline("->  ");

$stripped = explode(' ', $user);

if (!in_array($stripped[0], ['create', 'compile', 'generate'])) {
  echo 'Commande Inconnu';
  return;
}

if ($stripped[0] == 'create') {
  if ($stripped[1] == 'model') {
    if (!isset($stripped[2])) {
      echo "Erreur de commande :\n\tcreate model !!model_name!! model_table ?primary_key";
      return;
    }
    $model_name = $stripped[2];
    if (!isset($stripped[3])) {
      echo "Erreur de commande :\n\tcreate model model_name !!model_table!! ?primary_key";
      return;
    }
    $model_table = $stripped[3];
    $primary_key = 'id';

    if (isset($stripped[4]))
      $primary_key = $stripped[4];

    $file = fopen("app/Models/$model_name.php", "w");
    $string = include(__DIR__ . '/vendor/main/database/templates/model.php');
    $string = str_replace(['<<model_name>>', '<<model_table>>', '<<model_primary_key>>'], [$model_name, $model_table, $primary_key], $string);
    fwrite($file, $string);
    fclose($file);
  }

  if ($stripped[1] == 'controller') {
    if (!isset($stripped[2])) {
      echo "\n\tcreate controller !!controller_name!!";
      return;
    }
    $controller_name = $stripped[2];
    $file = fopen("app/Http/Controllers/$controller_name.php", "w");
    $string = include(__DIR__ . '/vendor/main/database/templates/controller.php');
    $string = str_replace("<<controller_name>>", $controller_name, $string);
    fwrite($file, $string);
    fclose($file);
  }

  if ($stripped[1] == 'middleware') {
    if (!isset($stripped[2])) {
      echo "\n\tcreate middleware !!middleware_name!!";
      return;
    }
    $middleware_name = $stripped[2];
    $file = fopen("app/Middleware/$middleware_name.php", "w");
    $string = include(__DIR__ . '/vendor/main/database/templates/middleware.php');
    $string = str_replace("<<middleware_name>>", $middleware_name, $string);
    fwrite($file, $string);
    fclose($file);
  }

  if ($stripped[1] == 'singleton') {
    if (!isset($stripped[2])) {
      echo "\n\tcreate singleton !!singleton_name!! ?singleton_var_name";
      return;
    }
    $singleton_name = $stripped[2];
    $singleton_var_name = $stripped[3] ?? "var";
    $file = fopen("app/Singleton/$singleton_name.php", "w");
    $string = include(__DIR__ . '/vendor/main/database/templates/singleton.php');
    $string = str_replace("<<singleton_name>>", $singleton_name, $string);
    $string = str_replace("<<singleton_var_name>>", $singleton_var_name, $string);
    fwrite($file, $string);
    fclose($file);
  }
} else if ($stripped[0] == 'compile') {
  /**
   * TODO: Review compiling views
   */
  // if ($stripped[1] == 'views') {
  //   $dir = ___DIR___ . '/resources';
  //   $files = Arrays::toStringData(Console::list_files_with_herine_extensions($dir), '/');
  //   foreach ($files as $file) {
  //     Compiler::compile_and_save(str_replace('.h.php', '', $file));
  //   }
  // } else 

  if ($stripped[1] == "routes") {
    eval(substr(file_get_contents(___DIR___ . '/app/Route/route.php'), 6));
    $routes = serialize(RouteSave::$routeSave);
    $config = include(___DIR___ . '/config/route.php');
    $path = ___DIR___ . ($config['route_save'] ?? '/storage/cache/routes/route.save');
    $file = fopen($path, 'w');
    fwrite($file, $routes);
    fclose($file);
  }
} else if ($stripped[0] == 'generate') {
  if ($stripped[1] == 'auth') {
    return Console::generate_auth();
  }
}
