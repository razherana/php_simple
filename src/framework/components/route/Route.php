<?php

namespace framework\components\route;

use framework\components\route\closure\ClosureWrapper;
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
    $r_uri = trim($this->uri, "\n\r\t\v\0/");
    $uri = trim($uri, "\n\r\t\v\0/");

    $vars = [];

    if (empty($uri)) return false;

    $r_uri_exp = explode('/', $r_uri);
    $uri_exp = explode('/', $uri);

    if (count($uri_exp) != count($r_uri_exp)) return false;

    for ($i = 0; $i < count($uri_exp); $i++) {
      if (
        stripos($r_uri_exp[$i], '<<') !== false
        && stripos($r_uri_exp[$i], '>>') !== false
      ) {
        $var_name = trim($r_uri_exp[$i], '<>');
        $var_name = trim($var_name);
        $vars[$var_name] = $uri_exp[$i];
      } else if ($uri_exp[$i] != $r_uri_exp[$i]) {
        return false;
      }
    }
    $this->vars = $vars;

    $ret = ArrayValidation::from($this->vars, $this->rules)->validate();

    if (RouteSave::$entered === $this && $ret === false) RouteSave::$entered = null;

    return $ret;
  }

  /**
   * Declare a new GET route
   * @param string $uri
   * @param array<Controller, string>|callable $callback
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
  public function save()
  {
    RouteSave::$all[] = $this;
    return $this;
  }

  /**
   * Runs the callback function
   */
  public function run_callback(): Response
  {
    if (is_array($this->callback)) {
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
