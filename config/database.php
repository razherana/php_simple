<?php

/**
 * Configuration of the Database component
 */
return [
  /**
   * Type of the database connection
   * Only supports mysql for the moment
   */
  "type" => "mysql",

  /**
   * Port of the connection (if mysql)
   */
  "port" => 3306,

  /**
   * The charset to use
   */
  "charset" => "utf8",

  /**
   * Sql file to imports to database
   */
  "imports" => [
    "script.sql"
  ]
];
