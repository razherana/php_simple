<?php

namespace vendor\main\database\queries\traits;

use vendor\main\database\queries\QueryMaker;
use vendor\main\util\Config;

trait SelectSql
{
  public $selected = [];
  public $asFrom = "";

  public function select($array = [], $add_prefix = true)
  {
    if (is_array($array)) {

      $this->selected = $array;
      return $this;
    } else if (is_string($array)) {
      $arr = $array::getAttributeInfo()->getAttributeName();
      $lol = $arr;

      if ($add_prefix) {

        if ($add_prefix === true) {
          $add_prefix = (Config::get('app', 'prefix_for_table') ?? '') . $array::$table;
          foreach ($arr as $k => $el) {
            $arr[$k] = $add_prefix . "." . $el;
          }
        } else if (is_string($add_prefix)) {
          $arr2 = [];
          foreach ($arr as $k => $v)
            $arr2[$v] = "`$add_prefix`." . $lol[$k];
          $arr = $arr2;
        }
      }

      return $this->select($arr);
    }
  }

  public function addSelect($array = [])
  {
    if (is_array($array)) {

      foreach ($array as $k => $v) {
        if (is_string($k)) {
          $this->selected[$k] = $v;
        } else {
          $this->selected[] = $v;
        }
      }

      return $this;
    } else if (is_string($array)) {
      $arr = $array::getAttributeInfo();
      $arr = $arr->getAttributeName();

      // array_walk($arr, fn (&$el) => $el = (Config::get('app', 'prefix_for_table') ?? '') . $array::$table . "." . $el);
      foreach ($arr as $k => $el) {
        $arr[$k] = (Config::get('app', 'prefix_for_table') ?? '') . $array::$table . "." . $el;
      }
      return $this->addSelect($arr);
    }
  }

  protected function decodeSelect()
  {
    $select = [];

    if (!$this->hasSelect()) {
      $this->addSelect(static::class);
    }

    foreach ($this->selected as $k => $v) {
      if (is_string($k) && !ctype_digit($k)) {
        $select[] = $k . " AS " . $v;
      } else {
        $select[] = $v;
      }
    }

    return implode(",", $select) . ' ';
  }

  public function hasSelect()
  {
    return !empty($this->selected);
  }

  public function asFrom($string)
  {
    $this->asFrom = "`$string`";
    return $this;
  }
}
