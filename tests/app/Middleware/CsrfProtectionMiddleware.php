<?php

namespace app\Middleware;

use app\Singleton\CsrfToken;
use vendor\main\uri\Url;

class CsrfProtectionMiddleware
{
  /**
   * Run the middleware before using a route
   */
  public static function run(): bool
  {
    if (Url::requestMethod() === "POST") {
      if (!isset(request()->post()['___csrf_token___'])) {
        return false;
      }
      return request()->post()['___csrf_token___'] === CsrfToken::get();
    }
    return true;
  }

  /**
   * If the middleware return false
   */
  public static function on_error()
  {
    abort_419();
    exit();
  }
}
