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

    // Checks the dir in route config (route_dir) if exist and a directory
    if (!is_dir($path))
      throw new RouteFileException("The route directory in config : '$path' is not a directory", 1);

    // Read all files from the route_files config
    foreach ($files as $file) if (is_file($current_route_file = $path . '/' . $file . '.php')) {
      include($current_route_file);
    }
  }

  public function config_file(): string
  {
    return "route";
  }
}
