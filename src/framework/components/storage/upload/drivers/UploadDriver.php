<?php

namespace framework\components\storage\upload\drivers;

use framework\components\storage\exceptions\FileException;
use framework\components\storage\Storage;
use framework\components\storage\upload\UploadManager;

abstract class UploadDriver
{
  public function __construct()
  {
    $this->create_folder();
  }

  /**
   * Creates the upload folder if not exists
   */
  protected function create_folder(): void
  {
    $upload_folder = (new UploadManager(false))->read_cached_config('folder');
    $storage_folder = (new Storage)->read_cached_config('folder');

    // Initialize ini vars
    $upload = ___DIR___ . "/" . trim($storage_folder, '/') . "/" . trim($upload_folder, '/');

    // If the upload dir doesn't exist
    if (!is_dir($upload))
      // Create the folder
      if (!mkdir($upload, 0777, true))
        // If cannot create folder, we throw
        throw new FileException("Cannot create the upload folder");
  }

  /**
   * Moves the temp upload file to the upload folder
   * @return array{id: string, path: string}
   */
  public function move($file): array
  {
    // Get the full path of the uploaded file after mapping
    $newname = $this->map($file);

    // Move the file to the designated folder
    move_uploaded_file($file['tmp_name'], $newname['path']);

    return $newname;
  }

  /**
   * Adds the file to upload map
   * @param array $file File info from $_FILES
   * @return array{id: string, path: string} Returns the new name with full path of the file. Throws exception on error
   */
  abstract protected function map($file): array;
}
