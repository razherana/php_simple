<?php

$START_TIME = microtime(true);

require_once(__DIR__ . '/../env.php');
require_once(___DIR___ . '/src/autoloader.php');
require_once(___DIR___ . '/src/function_autoloader.php');

use framework\base\Application;
use framework\components\database\Database;
use framework\components\debug\Debug;
use framework\components\route\Router;
use framework\components\session\Session;

global $app;

$app = Application::get();

/**
 * Initialize and Execute debug before anything
 * so it can run without other components
 */
$deb = new Debug;
$deb->initialize();
$deb->execute();

// Add components here
$components = [
  new Database,
  new Session,
  new Router,
];

$app->add_component($components);

$app->initialize();
$app->execute();

$EXECUTION_TIME = microtime(true) - $START_TIME;
