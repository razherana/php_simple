<?php

namespace vendor\main\cache;

use vendor\main\util\Json;

class CacheData
{
  private Json $data;
  const ASSOC_VALUE = 1, JSON_VALUE = 2, FROM_SESSION = 1000, FROM_OTHER = 1001;

  public function __construct($dir, $flag = self::FROM_OTHER)
  {
    if ($flag == self::FROM_OTHER && file_exists(___DIR___ . '/storage/cache/' . $dir . ".data"))
      $this->data = new Json(file_get_contents(___DIR___ . '/storage/cache/' . $dir . ".data"));
    else if ($flag == self::FROM_SESSION && file_exists(___DIR___ . '/storage/cache/' . getSessId() . ".data")) {
      $arr = json_decode((new Json(file_get_contents(___DIR___ . '/storage/cache/' . getSessId() . ".data")))->getArray(), true);
      $this->data = isset($arr[$dir]) ? new Json($arr[$dir]) : new Json('');
    } else $this->data = new Json('');
  }

  public function getData($flags = self::ASSOC_VALUE)
  {
    if ($flags == self::ASSOC_VALUE) {
      return $this->data->getArray();
    } else if ($flags == self::JSON_VALUE)
      return $this->data->getJson();
  }
}
