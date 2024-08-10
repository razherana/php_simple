<?php

return '<?php

namespace app\Middleware;

class <<middleware_name>>
{
  /**
   * Run the middleware before using a route
   */
  public static function run(): bool
  {
    // return [bool] ?? [fallback];
  }

  /**
   * If the middleware return false
   */
  public static function on_error()
  {
    // Do something
  }
}
';