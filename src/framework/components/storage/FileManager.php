<?php

namespace framework\components\storage;

use framework\components\storage\components\File;
use framework\components\storage\components\Folder;
use framework\components\storage\exceptions\FileException;

class FileManager
{
  /**
   * Get a File object starting from the storage folder
   * @param string $path The path to the file from storage directory
   */
  public static function open($path): File|Folder
  {
    if (File::is($path))
      return new File($path, true);
    elseif (Folder::is($path))
      return new Folder($path, true);

    throw new FileException("The file or folder doesn't exist");
  }

  /**
   * This force open a file
   * @param string $path The path to the file from storage directory
   */
  public static function file($path): File
  {
    return new File($path, true);
  }

  /**
   * This force open a folder
   * @param string $path The path to the file from storage directory
   */
  public static function folder($path): Folder
  {
    return new Folder($path, true);
  }
}
