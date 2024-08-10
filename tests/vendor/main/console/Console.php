<?php

namespace vendor\main\console;

class Console
{
  public static function list_files_with_herine_extensions($dir)
  {
    $ens = [];
    if (is_dir($dir))
      foreach (scandir($dir) as $file) {
        if ($file == '.' || $file == '..') continue;

        if (is_dir($dir . '/' . $file)) {
          $ens[$file] = self::list_files_with_herine_extensions($dir . '/' . $file);
        } else {
          $extensions = explode('.', basename($file));
          $nbr_dot = substr_count(basename($file), '.');

          if ($nbr_dot < 2) continue;
          if (
            $extensions[array_key_last($extensions)] == 'php' &&
            $extensions[array_key_last($extensions) - 1] == 'h'
          ) {
            $ens[] = $file;
          }
        }
      }
    return $ens;
  }

  public static function generate_auth()
  {
    $config_auth = include(___DIR___ . '/config/auth.php');
    $controller = include(___DIR___ . '/vendor/main/database/templates/auth/authcontroller.php');
    $controller = str_replace('<<config1>>', $config_auth['auth_id'], $controller);
    $controller = str_replace('<<config2>>', $config_auth['auth_pass'], $controller);
    $controller = str_replace('<<config3>>', $config_auth['model'], $controller);
    $model = explode('\\', $config_auth['model']);
    $model = $model[count($model) - 1];
    $controller = str_replace('<<config4>>', $model, $controller);
    $route = include(___DIR___ . '/vendor/main/database/templates/auth/route.php');

    if (!file_exists(___DIR___ . '/resources/auth')) {
      mkdir(___DIR___ . '/resources/auth');
    }

    copy(___DIR___ . "/vendor/main/database/templates/auth/views/auth/login.h.php", ___DIR___ . '/resources/auth/login.h.php');
    copy(___DIR___ . "/vendor/main/database/templates/auth/views/auth/register.h.php", ___DIR___ . '/resources/auth/register.h.php');

    $content = file_get_contents(___DIR___ . "/app/Route/route.php");
    $routes = fopen(___DIR___ . "/app/Route/route.php", "w");

    fwrite($routes, $content . $route);
    fclose($routes);

    $auth = fopen(___DIR___ . "/app/Http/Controllers/AuthController.php", "w");
    fwrite($auth, $controller);
    fclose($auth);

    if (!file_exists($dir = ___DIR___ . '/public/assets/css/auth')) {
      mkdir($dir, 0777, true);
    }

    copy(___DIR___ . "/vendor/main/database/templates/auth/views/css/login.css", ___DIR___ . '/public/assets/css/auth/login.css');
  }
}
