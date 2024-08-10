<?php

namespace app\Middleware;

use vendor\main\database\model\auth\Auth;

class AuthMiddleware
{
  /**
   * Run the middleware before using a route
   */
  public static function run(): bool
  {
    return Auth::loggedIn() ?? false;
  }

  /**
   * If the middleware return false
   */
  public static function on_error()
  {
    return to_route('auth.login');
  }
}
