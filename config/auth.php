<?php

/**
 * Contains auth config
 */
return [
  /**
   * Contains all of the defined auths
   * name_of_auth => [config_of_auth]
   */
  "auths" => [
    // "name_of_auth" => [Model::class, 'id_column'|['id_column1', 'id_column2', ...], 'pass_column', ?\Closure hash_method, ?\Closure password_verify],
  ],

  /**
   * This defines the default hash method
   * @param string $password
   * @return string The hashed password
   */
  "default_hash_method" => function ($password) {
    return password_hash($password, PASSWORD_BCRYPT);
  },

  /**
   * This defines the default password verifying method
   * @param string $password
   * @param string $hashed_password
   * @return bool
   */
  "default_password_verify" => function ($password, $hashed_password) {
    return password_verify($password, $hashed_password);
  },
];
