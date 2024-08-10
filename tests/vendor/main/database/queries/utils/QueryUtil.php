<?php

namespace vendor\main\database\queries\utils;

class QueryUtil
{
  const mysql_keys = [
    "now()",
    "null",
    "NULL"
  ];

  private function __construct()
  {
  }

  public static function sanitize($data)
  {
    array_walk($data, function (&$el) {
      $el = self::repairValue($el);
    });

    return $data;
  }

  public static function checkIfMySqlKey($string)
  {
    foreach (self::mysql_keys as $mysql) if (stripos($string, $mysql) !== false)
      return true;
    return false;
  }

  public static function repairValue($value)
  {
    if ($value === null) $value = "NULL";
    if (!is_numeric($value) && is_string($value) && !self::checkIfMySqlKey($value)) {
      $value = '"' . $value . '"';
    }
    return $value;
  }

  public static function allowOnlyFillable($model, $data)
  {
    $fillable = $model::$fillable;

    if (isset($fillable[0]) && $fillable[0] == '*') {
      return $data;
    }

    return array_filter($data, function ($el, $k) use ($fillable) {
      return in_array($k, $fillable);
    }, 1);
  }

  public static function allowOnlyModelColumns($modelName, $data)
  {
    $attNames = $modelName::getAttributeInfo()->getAttributeName();
    return array_filter($data, function ($el, $k) use ($attNames) {
      return in_array($k, $attNames);
    }, 1);
  }
}
