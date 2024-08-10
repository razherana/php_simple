<?php

namespace vendor\main\filing\ruling;

use vendor\main\filing\UploadedFile;

class FileRule
{
  private $file;
  public $errors = [];

  private function __construct($file)
  {
    $this->file = $file;
  }

  public static function file($file)
  {
    return new self($file);
  }

  /**
   * Whitelist
   * ['txt', 'img', ...]
   */
  public function extension($authorized_extensions = [])
  {
    $res = $this->file->checkExtension($authorized_extensions);
    if (!$res) {
      $this->errors[] = "Extension non autorisé : " . $this->file->getExtension();
    }
    return $this;
  }

  /**
   * Check if the file is bigger than $size_in_byte
   */
  public function size($size_in_byte)
  {
    $size1 = $this->file->size;
    $size2 = filesize($this->file->tmp_name);

    if ($size1 > $size_in_byte || $size2 > $size_in_byte) {
      $this->errors[] = "La taille du fichier dépasse les $size_in_byte octets : " . max($size1, $size2);
    }
    return $this;
  }
}
