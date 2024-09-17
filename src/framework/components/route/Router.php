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

    $middleware = new MiddlewareExecuter($route->middlewares, $this->request);

    $middleware->execute();

    $response = $route->run_callback();

    return $response->send();
  }

  public function initialize()
  {
    $this->request = Request::get_from_global_vars();

    /** @var string $path */
    $path = ___DIR___ . '/' . $this->read_cached_config('route_dir');

    /** @var string[] */
    $save_files = $this->read_cached_config('route_saved');

    /** @var string */
    $route_storage = trim($this->read_cached_config('route_storage'), '/');

    $faster_checking = empty($save_files);

    $full_storage_path = ___DIR___ . '/' . $route_storage;

    // Creates the directory if doesn't exist
    if (!is_dir($full_storage_path)) {
      $dir_creation_result = mkdir($full_storage_path, 0777, true);

      if (!$dir_creation_result)
        throw new RouteFileException("Cannot create the saved route directory in '$full_storage_path' is not a directory");
    }

    /** @var array $files */
    $files = $this->read_cached_config('route_files');

    // Checks the dir in route config (route_dir) if exist and a directory
    if (!is_dir($path))
      throw new RouteFileException("The route directory in config : '$path' is not a directory");

    // Read all files from the route_files config
    foreach ($files as $file) if (is_file($current_route_file = $path . '/' . $file . '.php')) {

      // Use faster checking to ignore the in_array if the save files is empty 
      if ($faster_checking || !in_array($file, $save_files)) {
        include($current_route_file);
        continue;
      }

      // Makes the complete path for the current route 
      $full_storage_route_path = $full_storage_path . "/$file.route_save";

      // Checks if the file exist
      if (is_file($full_storage_route_path)) {
        /** @var Route[] */
        $routes = unserialize(file_get_contents($full_storage_route_path));
        RouteSave::$all[$file] = $routes;
        continue;
      }

      // Includes the route file
      include($current_route_file);

      // Serialize the route file
      $serialized_route = serialize(RouteSave::$all[$file]);

      // Write the route content
      if (file_put_contents($full_storage_route_path, $serialized_route) === false)
        throw new RouteFileException("Cannot create the save route file");
    }
  }

  public function config_file(): string
  {
    return "route";
  }
}
