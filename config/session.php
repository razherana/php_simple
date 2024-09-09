<?php

/**
 * Contains the config of the session
 */
return [
  /**
   * The driver to use for the session
   * Compatible driver : ['file', 'mysql']
   */
  "driver" => "mysql",

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
];
