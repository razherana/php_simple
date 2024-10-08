<?php

use http\middlewares\AuthMiddleware;
use http\middlewares\CsrfMiddleware;

/**
 * This contains all config for middlewares
 */
return [
  /**
   * All middleware aliases
   */
  "aliases" => [
    // "some_middleware_alias_to_use" => SomeMiddleware::class
    "csrf" => CsrfMiddleware::class,
    "auth" => AuthMiddleware::class,
  ],

  /**
   * Contains all middleware to add automatically
   */
  "auto" => [
    'csrf'
  ],
];
