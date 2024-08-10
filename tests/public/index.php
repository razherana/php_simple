<?php

use vendor\main\util\Config;

require_once("../env.php");
require_once(___DIR___ . "/vendor/autoloader.php");
require_once(___DIR___ . "/vendor/main/base_functions.php");

date_default_timezone_set(Config::get('app', 'php_timezone') ?? 'Africa/Nairobi');

if (Config::get('app', 'debug_mode') === false) {
  require_once(___DIR___ . '/vendor/main/macro/undisplay_errors.php');
}

require_once(___DIR___ . '/vendor/main/macro/session_start_protection.php');

require_once(___DIR___ . "/vendor/main/macro/set_default_temp.php");
require_once(___DIR___ . "/vendor/main/macro/execute_route.php");
require_once(___DIR___ . "/vendor/main/macro/delete_temp.php");
