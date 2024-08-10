<?php

namespace vendor\main\cache;

require_once(__DIR__ . '/../../../env.php');
require_once(__DIR__ . '/../base_functions.php');
require_once(__DIR__ . '/../Facades.php');
require_once(__DIR__ . '/../util/Json.php');

use Countable;
use vendor\main\util\Json;
use vendor\main\Facades;

class Caching extends Facades
{
  const dir = ___DIR___ . "/storage/cache";

  /**
   * Ecrase l'intérieur
   */
  const JSON_ECRASE = 1;

  /**
   * Ajoute des valeurs à la fin
   */
  const JSON_PUSH = 2;

  public static $functions = [
    "cache" => "_cacheData",
    "cacheSession" => "_cacheDataSession"
  ];

  /** 
   * @param string $name
   * @param Json $to_store
   * @param int $flags = JSON_ECRASE
   */
  public function _cacheData($array)
  {
    $name = $array[0];
    $to_store = $array[1];
    $flags = isset($array[2]) ? $array[2] : self::JSON_ECRASE;
    $filedir = self::dir . "/" . $name . ".data";
    if (file_exists($filedir)) {
      $file_content = file_get_contents($filedir);
      if ($flags == self::JSON_ECRASE) {
        $file_content = $to_store->getJson();
      } else if ($flags == self::JSON_PUSH) {
        $file_content = json_decode($file_content, true);
        $arr = $to_store->getArray();
        if (!is_array($file_content)) {
          $file_content = [$file_content];
        }
        if (is_array($arr))
          $file_content[array_key_first($arr)] = $arr[array_key_first($arr)];
        else
          array_push($file_content, $arr);
        file_put_contents($filedir, (new Json((array) ($file_content)))->getJson()) !== false;
      }
    }
    $content = false;
    if (file_exists($filedir))
      $content = file_get_contents($filedir);
    if ($content !== false && $content !== '') {
      $file_content = (new Json($content, true));
      $file_content->pushJson($to_store->getJson());
      $file_content = $file_content->getJson();
    } else {
      $file_content = $to_store->getJson();
    }
    $file = fopen($filedir, "w");
    fwrite($file, $file_content);
    fclose($file);
  }

  public function _cacheDataSession($data)
  {
    $name = $data[0];
    $data[0] = getSessId();
    $datas = [];
    $datas[$name] = $data[1]->getArray();
    $data[1] = new Json($datas);
    return $this->_cacheData($data);
  }
}
