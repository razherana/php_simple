<?php

use vendor\main\routing\singleton\RouteSave;

$config_route = require(___DIR___ . '/config/route.php');

if ($config_route['reload_route'] === false ?? false) {
  $enter = true;

  RouteSave::$routeSave = unserialize(file_get_contents(___DIR___ . ($config['route_save'] ?? '/storage/cache/routes/route.save')));
}

require_once(___DIR___ . "/app/Route/route.php");

if (RouteSave::$routeEntered !== null) {
  RouteSave::$routeEntered->callMethod();
} else {
  abort_404();
}
