<?php

namespace vendor\main\request;

use vendor\main\util\Arrays;
use vendor\main\Facades;
use vendor\main\filing\FileManager;
use vendor\main\uri\Url;

class Request extends Facades
{
  public static $functions = [];
  private $uri;

  public function __construct()
  {
    $this->uri = new Url;
  }

  private static function sanitizeInputs($arr)
  {
    foreach ($arr as $k => $el) {
      if ($el == "") $arr[$k] = null;
      if ($el == "off") $arr[$k] = false;
      if ($el == "on") $arr[$k] = true;
    };
    return $arr;
  }

  public function post()
  {
    if (Url::requestMethod() == "POST")
      return self::sanitizeInputs($_POST);
    return null;
  }

  public function query($to_array = true)
  {
    if ($this->uri->getQueryFacade() === null) return [];
    if (!$to_array) {
      return $this->uri->getQueryFacade();
    }
    return Arrays::fromQuery($this->uri->getQueryFacade());
  }

  public function file($name = '')
  {
    if ($name == '')
      return $_FILES;
    return FileManager::getUploadedFile($name);
  }
}
