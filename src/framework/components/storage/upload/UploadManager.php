<?php

namespace framework\components\storage\upload;

use framework\base\config\ConfigurableElement;
use framework\components\storage\components\File;
use framework\components\storage\StorageManager;
use framework\components\storage\upload\drivers\UploadDriver;
use framework\components\storage\upload\exceptions\UploadDriverException;

class UploadManager extends ConfigurableElement
{
  /**
   * This contains the upload driver to use
   * @var ?UploadDriver $driver
   */
  protected $driver = null;

  public function config_file(): string
  {
    return 'upload';
  }

  /**
   * @param $driver The upload driver's class to use or readconfig to just use the config's driver
   */
  public function __construct($driver = "readconfig")
  {
    // In case we only want the config_reader of the upload
    if ($driver === false) return;
    $driver_class = $driver;

    if ($driver == "readconfig") {
      $available_drivers = $this->read_cached_config('available_drivers');
      $driver_to_use = $this->read_cached_config('driver');

      if (!isset($available_drivers[$driver_to_use]))
        throw new UploadDriverException("The driver '$driver_to_use' doesn't exist");

      $driver_class = $available_drivers[$driver_to_use];
    }

    $this->driver = new $driver_class();
  }

  public function save($file)
  {
    return $this->driver->move($file);
  }

  /**
   * Get a File object starting from the storage folder
   * @param string $path The path to the file from storage directory
   */
  public function open($id): File
  {
    $folder = trim($this->read_cached_config('folder'), '\/');
    $full_path = "$folder/$id.uploadedfile";
    return StorageManager::open($full_path);
  }
}
