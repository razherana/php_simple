<?php

namespace vendor\main\database\queries\traits;

trait OrderSql
{
  public $orders = [];
  private static $ASC = "ASC", $DESC = "DESC";

  public function _orderByAsc($column = '')
  {
    $this->orders[] = [$column, self::$ASC];
    return $this;
  }

  public static function orderByAscStatic($column = '', $use = null)
  {
    $e = new static($use);
    $e->orders[] = [$column, self::$ASC];
    return $e;
  }

  public static function orderByDescStatic($column = '', $use = null)
  {
    $e = new static($use);
    $e->orders[] = [$column, self::$DESC];
    return $e;
  }

  public function _orderByDesc($column = '')
  {
    $this->orders[] = [$column, self::$DESC];
    return $this;
  }

  protected static function decodeGivenOrder($element)
  {
    return "ORDER BY " . $element[0] . " " . $element[1];
  }

  public function decodeOrders()
  {
    $ens = [];
    foreach ($this->orders as $e)
      $ens[] = self::decodeGivenOrder($e);
    return implode(" ", $ens) . " ";
  }

  public function hasOrders()
  {
    return !empty($this->orders);
  }
}
