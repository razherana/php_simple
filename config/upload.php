<?php

/**
 * Configs for upload
 */

use framework\components\storage\upload\drivers\UploadFileDriver;
use framework\components\storage\upload\drivers\UploadMysqlDriver;

return [
  /**
   * Upload directory
   */
  "folder" => "upload",

  /**
   * List of the available upload drivers
   */
  "available_drivers" => [
    "file" => UploadFileDriver::class,
    "mysql" => UploadMysqlDriver::class,
  ],

  /**
   * Upload manager driver
   * Supported drivers, see available_drivers
   */
  "driver" => "file",

  // File driver only

  /**
   * Map file
   */
  "file_map" => "map/uploads.json",


  // Mysql driver only

  /**
   * Mysql table name
   */
  "mysql_tablename" => "__uploads__",

  /**
   * Mysql table query
   */
  "mysql_tablequery" => "CREATE TABLE IF NOT EXISTS <<upload>> (id INT PRIMARY KEY AUTO_INCREMENT, name VARCHAR(100), created_at DATETIME DEFAULT NOW())"
];
