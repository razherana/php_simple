<?php

namespace vendor\main\filing\downloading;

use vendor\main\uri\Url;

/**
 * Cannot verify, just download. Please sanitize the file and the person who downloads it
 */
class Download
{
  public $fileName = '';

  private function __construct($fileName)
  {
    $this->fileName = $fileName;
  }

  public static function fileName($fileName)
  {
    return new static($fileName);
  }

  public function download($link_to_redirect = '', $isRoute = false)
  {
    $fileName = $this->fileName;

    require_once ___DIR___ . '/vendor/main/macro/header_download.php';

    readfile($this->fileName);

    ob_clean();
    flush();

    if ($link_to_redirect != '') {
      if ($isRoute) {
        return to_route($link_to_redirect);
      }
      return Url::redirect($link_to_redirect);
    }
    return $this;
  }
}
