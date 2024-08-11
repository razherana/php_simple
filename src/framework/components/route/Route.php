<?php

namespace framework\components\route;

use framework\components\route\closure\ClosureWrapper;
use framework\components\route\exceptions\RouteException;
use framework\components\route\storage\RouteSave;
use framework\http\handler\BaseController;
use framework\http\Request;
use framework\http\Response;
use framework\rule\Rule;
use framework\rule\validation\ArrayValidation;

class Route
{
  /**
   * This contains the request
   * to use during the callback
   * @var Request $request
   */
  private $request;

  /**
   * This contains the rules for route vars
   * @var Rule[] $rules
   */
  private $rules = [];

  /**
   * This contains special vars
   * of the uri '<<var>>'
   * @var array $vars
   */
  private $vars = [];

  public function __construct(
    /**
     * The method of the request
     * @var string $method
     */
    public $method = "GET",

    /**
     * The route's name
     * @var string $name
     */
    public $name = "",

    /**
     * The route's middlewares
     * @var string[] $middlewares
     */
    public $middlewares = [],

    /**
     * The route's callback
     * @var array<Controller, string>|callable $callback
     */
    public $callback = null,

    /**
     * The route's uri
     * @var string $uri
     */
    public $uri = '',
  ) {
    $this->request = Request::getFromGlobalVars();
    $uri = $this->request->request_uri();

    $this->save_if_current($uri);

    if ($callback instanceof \Closure) {
      $this->callback = ClosureWrapper::from($callback);
    }
  }

  /**
   * Checks if $uri is the same as $this->uri and 
   * save if it is
   * @param string $uri
   */
  private function save_if_current($uri)
  {
    if ($this->testIfCurrent($uri))
      RouteSave::$entered = $this;
  }

  public function __wakeup()
  {
    $this->request = Request::getFromGlobalVars();
  }

  /**
   * Add rules to route vars
   * @param Rule[] $rules
   */
  public function rules($rules)
  {
    $this->rules = array_merge($this->rules, $rules);

    $this->request = Request::getFromGlobalVars();
    $uri = $this->request->request_uri();

    $this->save_if_current($uri);

    return $this;
  }

  /**
   * This method checks if the current route is
   * requested by the client
   * @param string $uri
   */
  public function testIfCurrent($uri): bool
  {
    // Checks if the route_uri has << >> which means it has route_vars
    if (strpos($this->uri, '<<') !== false && strpos($this->uri, '>>')) {
      return $this->checkWithVar($uri);
    }
    return $this->uri == $uri;
  }

  /**
   * This method checks if the current route is
   * requested by the client but the uri
   * contains variables
   * @param string $uri
   */
  private function checkWithVar($uri)
  {
    // This will be the return value
    $ret = true;

    // Trim both uri from default trim and /
    $r_uri = trim($this->uri, "\n\r\t\v\0/");
    $uri = trim($uri, "\n\r\t\v\0/");

    $vars = [];

    // Checks if the uri is empty or blank (1)
    if (empty($uri)) return false;

    $r_uri_exp = explode('/', $r_uri);

    // explode(smth, '') === false so we need to check before, see (1)
    $uri_exp = explode('/', $uri);

    if (count($uri_exp) != count($r_uri_exp)) return false;

    for ($i = 0; $i < count($uri_exp); $i++) {
      if (
        // Check if the current element is a var
        stripos($r_uri_exp[$i], '<<') !== false
        && stripos($r_uri_exp[$i], '>>') !== false
      ) {
        // Trim from < and > to only let the var_name there
        $var_name = trim($r_uri_exp[$i], '<>');

        // Default trim
        $var_name = trim($var_name);

        /**
         * We correlate the values in route uri and request_uri
         * so we can have a 'var_name' => $value_in_request_uri
         */
        $vars[$var_name] = $uri_exp[$i];
      } else if (
        // If this isn't a route var then default check route_uri and request_uri
        $uri_exp[$i] != $r_uri_exp[$i]
      ) {
        // Sets the return value to false
        $ret = false;
      }
    }
    // Sets the vars
    $this->vars = $vars;

    // Checks that the route_vars and request_uri correlate before validating
    // Faster check
    if ($ret === true)
      $ret = ArrayValidation::from($this->vars, $this->rules)->validate();

    // Checks if the route entered is $this but after checking it isn't
    if (RouteSave::$entered === $this && $ret === false) RouteSave::$entered = null;

    return $ret;
  }

  /**
   * Declare a new GET route
   * @param string $uri
   * @param array<Controller::class, string>|callable $callback
   */
  public static function get($uri = '', $callback = null): self
  {
    return new self("GET", "", [], $callback, $uri);
  }

  /**
   * Declare a new POST route
   * @param string $uri
   * @param array<Controller, string>|callable $callback
   */
  public static function post($uri = '', $callback = null): self
  {
    return new self("POST", "", [], $callback, $uri);
  }

  /**
   * Set the route's name
   * @param string $name
   */
  public function name($name)
  {
    $this->name = $name;
    return $this;
  }

  /**
   * Set the route's method
   * @param string $method
   */
  public function method($method)
  {
    $this->method = $method;
    return $this;
  }

  /**
   * Set the route's uri
   * @param string $uri
   */
  public function uri($uri)
  {
    $this->uri = $uri;
    return $this;
  }

  /**
   * Add middleware(s) to the route
   * @param string ...$middlewares
   */
  public function middleware(...$middlewares)
  {
    $this->middlewares = $middlewares;
    return $this;
  }

  /**
   * Set the middlewares to the route
   * @param string[] $middlewares
   */
  public function set_middleware($middlewares)
  {
    $this->middlewares = $middlewares;
    return $this;
  }

  /**
   * Saves the route, use if you want to use this route in other part of your script
   */
  public function save(): void
  {
    // Add it inside an associative array where key = file name

    RouteSave::$all[str_replace(

      // Search for route_dir
      (___DIR___ . '/' . (new Router)->read_config('route_dir')),

      // And replace it with blank, so it removes the route_dir
      '',

      // Remove the .php extension
      substr(__FILE__, strlen(__FILE__) - 4)

    )][] = $this;
  }

  /**
   * Runs the callback function
   */
  public function run_callback(): Response
  {
    if (is_array($this->callback)) {
      // Checks that the callback is BaseController or his child else throw Exception
      if ($this->callback[0] !== BaseController::class && get_parent_class($this->callback[0]) !== BaseController::class) {
        throw new RouteException('The controller given on the callback of this route is not a BaseController or a BaseController childs but a ' . $this->callback[0]);
      }

      $controller = new $this->callback[0]($this->request);
      $content = $controller->{$this->callback[1]}(...array_values($this->vars));
    } else {
      $controller = new BaseController($this->request);

      $callback = ($this->callback->get_closure())->bindTo($controller, $controller::class);

      $content = $callback(...array_values($this->vars));
    }
    return $controller->get_response($content);
  }
}
