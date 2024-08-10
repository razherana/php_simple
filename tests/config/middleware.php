<?php

/**
 * Define the aliases of your middlewares here
 */

use app\Middleware\AdminAuthMiddleware;
use app\Middleware\AuthMiddleware;
use app\Middleware\CsrfProtectionMiddleware;

return [
  /**
   * Middleware who runs automatically
   */
  "autorunned_middlewares" => [
    'csrf'
  ],

  /**
   * Define here your middlewares
   */
  "auth" => AuthMiddleware::class,
  "csrf" => CsrfProtectionMiddleware::class,
  // "admin_auth" => AdminAuthMiddleware::class,
];
