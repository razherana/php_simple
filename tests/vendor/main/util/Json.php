<?php

namespace vendor\main\util;


class Json
{
  private $json;

  public function getArray()
  {
    return json_decode($this->json, true);
  }

  public function __construct($data, $isAlreadyJson = false)
  {
    if ($isAlreadyJson) {
      $this->json = $data;
    } else {
      if (is_int($data)) {
        $this->json = json_encode((int)($data));
      }
      if (is_string($data)) {
        $this->json = json_encode($data);
      }
      if (is_array($data))
        $this->json = json_encode($data);
    }
  }

  public function getJson()
  {
    return $this->json;
  }

  public function pushJson($string)
  {
    $arr = json_decode($this->json, true);
    $arr_2 = json_decode($string, true);
    if (is_null($arr_2)) {
      $arr[] = $arr_2;
    } else {
      $arr += $arr_2;
    }
    $this->json = json_encode($arr, true);
  }
}
