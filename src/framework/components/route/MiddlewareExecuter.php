<?php

namespace framework\components\route;

use framework\base\config\ConfigurableElement;
use framework\components\route\exceptions\MiddlewareExecuterException;
use framework\http\handler\BaseMiddleware;
use framework\http\Request;

/**
 * This class is the middleware executer
 */
class MiddlewareExecuter extends ConfigurableElement
{
  public function config_file(): string
  {
    return 'middleware';
  }

  /**
   * Contains all middlewares to check from
   * @var array<string,string> $middlewares
   */
  protected $middlewares = [];

  /**
   * Contains the request to use
   * @var Request $request
   */
  protected $request;

  /**
   * @param string[] $middlewares
   * @param ?Request $request If request is null, we make from a global var
   */
  public function __construct($middlewares, $request = null)
  {
    $middlewares_instances = [];
    $all_middleware = $this->read_cached_config('aliases');
    $auto = $this->read_cached_config('auto');

    $middlewares =
      // We remove duplicates
      array_unique(
        // We merge the arrays
        array_merge($middlewares, $auto)
      );

    foreach ($middlewares as $middleware) {
      preg_match("/(\\w+)(?:\:(.*))?/", $middleware, $args);

      $middleware = $args[1];

      if (count($args) >= 3)
        $args = explode(',', $args[2]);

      if (!isset($all_middleware[$middleware]))
        throw new MiddlewareExecuterException("This middleware doesn't have an alias : '$middleware'");

      $middleware_class = $all_middleware[$middleware];

      if (!is_a($middleware_class, BaseMiddleware::class, true))
        throw new MiddlewareExecuterException("The middleware given is not a Middleware class but a '" . $middleware_class . "'");

      $middlewares_instances[$middleware] = [$middleware_class, $args];
    }

    $this->middlewares = $middlewares_instances;

    if ($request === null)
      $request = Request::get_from_global_vars();

    $this->request = $request;
  }

  /**
   * Executes all of the middlewares
   * @return int The number of middleware executed
   */
  public function execute(): int
  {
    $result = 0;

    foreach ($this->middlewares as $middleware_class) {
      /** @var BaseMiddleware $middleware */
      $middleware = new $middleware_class[0]($this->request, ...($middleware_class[1] ?? []));

      if ($middleware->checks()) {
        $middleware->execute();
        $result++;
      }
    }

    return $result;
  }
}
