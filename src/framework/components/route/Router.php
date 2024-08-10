<?php

namespace framework\components\route;

use framework\base\Component;
use framework\http\Request;
use framework\base\config\ConfigurableElement;
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
    $path = $this->read_config('route_files');

    if (!is_dir($path))
      throw new \Exception("$path is not a directory", 1);

    if (($dir = opendir($path)) === false)
      throw new \Exception("Failed to open the directory $path", 1);

    while ($file_name = readdir($dir)) {
      if (is_file(($full_dir_file = $path . '/' . $file_name)) && substr($file_name, strlen($file_name) - 4) == '.php')
        include($full_dir_file);
    }
  }

  public function config_file(): string
  {
    return "route";
  }
}
