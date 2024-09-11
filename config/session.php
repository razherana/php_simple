<?php

/**
 * Contains the config of the session
 */

use framework\components\database\auth\Auth;
use framework\components\session\SessionManager;

return [
  /**
   * The driver to use for the session
   * Compatible driver : ['file', 'mysql']
   */
  "driver" => "file",

  /**
   * Directory of session if file 
   */
  "file_directory" => "storage/session",

  /**
   * Table for the session in database
   */
  "mysql_table" => "___sessions___",

  /**
   * Table structure for mysql_table
   */
  "mysql_structure" => "CREATE TABLE IF NOT EXISTS <<session>> (id INT PRIMARY KEY AUTO_INCREMENT, id_session CHAR(26), content MEDIUMBLOB DEFAULT NULL, created_at DATETIME DEFAULT NOW())",

  /**
   * Contains the reserved keywords for session
   * @var string[] Array of classes
   */
  "reserved_keywords" => [
    SessionManager::class,
    Auth::class
  ],

  /**
   * Contains the array to add reserved keywords
   * The variable must be a static variable
   * @var array<string, string>
   */
  "add_reserved_keywords" => [
    SessionManager::class => 'RESERVED_KEYS',
  ],

  /**
   * Contains classes to initialize sessions
   */
  "initialize_session" => [
    SessionManager::class,
  ],
];
