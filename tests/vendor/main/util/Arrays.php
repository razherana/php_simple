<?php

namespace vendor\main\util;

class Arrays
{
  private function __construct()
  {
  }

  public static function fromQuery($query)
  {
    $stripped = explode('&', $query);
    $ens = [];
    foreach ($stripped as $v) {
      $str = explode('=', $v);
      $sub = substr($str[0], strlen($str[0]) - 2);
      $sub1 = substr($str[0], 0, strlen($str[0]) - 2);
      if ($sub == '[]') {
        if (array_key_exists($sub1, $ens)) {
          $ens[$sub1][] = $str[1];
        } else {
          $ens[$sub1] = [$str[1]];
        }
      } else {
        $ens[$str[0]] = $str[1];
      }
    }
    return $ens;
  }

  public static function invertValuesAndKeys($array)
  {
    $keys = array_keys($array);
    return array_combine($keys, array_keys($keys));
  }

  public static function addArray($array1, $array2)
  {
    foreach ($array1 as $k => $v) {
      if (!isset($array2[$k])) {
        $array2[$k] = $v;
      } else {
        if (is_array($array2[$k])) {
        }
      }
      if (is_array($v))
        $array2 = self::addArray($v, $array2);
    }
    return $array2;
  }

  /**
   * @param string[] $array
   */
  public static function toStringData($array, $separator, $prefix = '')
  {
    $ens = [];
    foreach ($array as $key => $val) {
      if (is_array($val)) {
        foreach (self::toStringData($val, $separator, $key . $separator) as $v)
          $ens[] = ($prefix !== '' ? ($prefix . $separator) : '') . $v;
      } else {
        $ens[] = $prefix . $val;
      }
    }
    return $ens;
  }

  public static function sort($array, $callable)
  {
    for ($i = 0, $keys = array_keys($array); $i < count($array) - 1; $i++)
      for ($j = 0; $j < count($array) - $i - 1; $j++)
        if ($callable($array[$keys[$j]]) > $callable($array[$keys[$j + 1]])) {
          $temp = $array[$keys[$j]];
          $array[$keys[$j]] = $array[$keys[$j + 1]];
          $array[$keys[$j + 1]] = $temp;
        }
    return $array;
  }

  public static function rsort($array, $callable)
  {
    for ($i = 0, $keys = array_keys($array); $i < count($array) - 1; $i++)
      for ($j = 0; $j < count($array) - $i - 1; $j++)
        if ($callable($array[$keys[$j]]) < $callable($array[$keys[$j + 1]])) {
          $temp = $array[$keys[$j]];
          $array[$keys[$j]] = $array[$keys[$j + 1]];
          $array[$keys[$j + 1]] = $temp;
        }
    return $array;
  }

  public static function unique($array, $callable, $keep_keys = false)
  {
    $used = [];
    $return = [];
    foreach ($array as $k => $el) {
      $v = $callable($el, $k);

      if (in_array($v, $used)) continue;
      $used[] = $v;

      if ($keep_keys)
        $return[$k] = $el;
      else
        $return[] = $el;
    }
    return $return;
  }

  public static function unique_prefer($array, $unique, $prefer)
  {
    $used = [];
    foreach ($array as $k => $el) {
      $v = $unique($el, $k);

      if (in_array($v, array_keys($used))) {
        if ($prefer($el, $used[$k]))
          $used[$v] = $el;
      } else {
        $used[$v] = $el;
      }
    }
    return array_merge($used);
  }

  public static function group($array, $to_group, $keep_keys = false)
  {
    $return = [];
    foreach ($array as $k => $v) {
      $key = $to_group($v, $k);

      if (!isset($return[$key]))
        $return[$key] = [];

      if ($keep_keys)
        $return[$key][$k] = $v;
      else
        $return[$key][] = $v;
    }
    return $return;
  }

  public static function filter($array, $filter_callable, $keep_keys = false)
  {
    $return = [];

    foreach ($array as $k => $v) if ($filter_callable($v, $k)) {
      if ($keep_keys)
        $return[$k] = $v;
      else
        $return[] = $v;
    }

    return $return;
  }
}
