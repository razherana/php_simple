<?php

namespace framework\components\storage\upload\drivers;

use framework\components\storage\Storage;
use framework\components\storage\upload\UploadManager;

class UploadFileDriver extends UploadDriver
{
  protected function map($file): array
  {
    $json_file = (new UploadManager(false))->read_cached_config('file_map');
    $storage_folder = (new Storage)->read_cached_config('folder');
    $upload_folder = (new UploadManager(false))->read_cached_config('folder');

    $upload_folder = ___DIR___ . "/" . trim($storage_folder, '/') . "/" . trim($upload_folder, '/');
    $json_file = ___DIR___ . "/" . trim($storage_folder, '/') . "/" . trim($json_file, '/');

    // Get the content of the map
    $content = [];
    if (file_exists($json_file))
      // If map already exist, we use that
      $content = json_decode(file_get_contents($json_file));

    // Make the map
    $new_map = [
      "name" => $file['name'],
      "full_path" => $file['full_path']
    ];

    // Writes the id in a variable
    // This will be returned as the fileid
    $fileid = count($content);

    // Adds the new map to the content
    $content[] = $new_map;

    // Writes the map to the file
    file_put_contents($json_file, json_encode($content));

    // We return the info's
    return [
      "id" => $fileid,
      "path" => $upload_folder . "/" . $fileid . ".uploadedfile",
    ];
  }
}
