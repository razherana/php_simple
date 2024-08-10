<?php

namespace vendor\main\filing\image;

use app\Models\File;

class Image
{
  private $image;

  private function __construct($image)
  {
    $this->image = $image;
  }

  public static function from($image)
  {
    if ($image->modelName !== File::class) {
      throw new \Exception("Not a File", 1);
    }

    return new self($image);
  }

  /**
   * Use when showing the image in a route
   */
  public function read()
  {
    header("Content-Type: " . $this->image->mime);
    readfile(File::UPLOADS_FOLDER . $this->image->name);
  }

  public function parseHtml()
  {
    $image = $this->image;

    $fileName = File::UPLOADS_FOLDER . $image->name;

    $image_content = file_get_contents($fileName);

    $q = "data:" . $image->mime . ';base64,';

    return $q . base64_encode($image_content);
  }
}
