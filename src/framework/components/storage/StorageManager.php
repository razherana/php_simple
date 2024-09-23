<?php

namespace framework\components\storage;

use framework\components\storage\components\File;
use framework\components\storage\components\Folder;

class StorageManager extends FileManager
{
  /**
   * Get a File object starting from the storage folder
   * @param string $path The path to the file from storage directory
   */
  public static function open($path): File|Folder
  {
    $folder = trim((new Storage)->read_cached_config('folder'), '\/');
    $full_path = "$folder/$path";
    return parent::open($full_path);
  }

  /**
   * This force open a file
   * @param string $path The path to the file from storage directory
   */
  public static function file($path): File
  {
    $folder = trim((new Storage)->read_cached_config('folder'), '\/');
    $full_path = "$folder/$path";
    return parent::file($full_path);
  }

  /**
   * This force open a folder
   * @param string $path The path to the file from storage directory
   */
  public static function folder($path): Folder
  {
    $folder = trim((new Storage)->read_cached_config('folder'), '\/');
    $full_path = "$folder/$path";
    return parent::folder($full_path);
  }
}
