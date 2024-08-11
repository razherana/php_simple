<?php

namespace framework\components\route;

use framework\base\Component;
use framework\http\Request;
use framework\base\config\ConfigurableElement;
use framework\components\route\exceptions\RouteFileException;
use framework\components\route\storage\RouteSave;
use framework\http\Response;

class Router extends ConfigurableElement implements Component
{
  /**
   * @var Request $request 
   */
  protected $request;

  public function execute()
  {
    /**
     * Execute the controller of the entered route
     * Or abort 404
     */
    if (RouteSave::$entered === null)
      return Response::abort(404);

    $route = RouteSave::$entered;

    $response = $route->run_callback();

    return $response->send();
  }

  public function initialize()
  {
    $this->request = Request::getFromGlobalVars();

    /**
     * @var string $path
     */
    $path = ___DIR___ . '/' . $this->read_config('route_dir');

    /**
     * @var array $files
     */
    $files = $this->read_config('route_files');

    // Add .php extension to each route files
    foreach ($files as &$route_name) $route_name = $route_name . '.php';

    if (!is_dir($path))
      throw new RouteFileException("The route directory in config : '$path' is not a directory", 1);

    if (($dir = opendir($path)) === false)
      throw new RouteFileException("Failed to open the directory of route path : '$path'", 1);

    while ($file_name = readdir($dir)) {
      if (
        is_file(($full_dir_file = $path . '/' . $file_name))
        && in_array($file_name, $files)
      )
        include($full_dir_file);
    }
  }

  public function config_file(): string
  {
    return "route";
  }
}
