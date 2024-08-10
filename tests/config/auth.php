<?php

use app\Models\Admin;
use app\Models\User;

return [
  /**
   * Model to use for auth
   */
  "model" => User::class,

  /**
   * The column name to identify on login (must be unique)
   */
  "auth_id" => "email",

  /**
   * The column name to log into the account
   */
  "auth_pass" => "password",

   /**
    * Insert here custom Auth
    */
  // "admin_custom" => [Admin::class, 'name', 'password', '___admin_user___']
];
