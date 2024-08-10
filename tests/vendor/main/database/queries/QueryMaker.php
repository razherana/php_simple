<?php

namespace vendor\main\database\queries;

use vendor\main\database\queries\traits\JoinsSql;
use vendor\main\database\queries\traits\OrderSql;
use vendor\main\database\queries\traits\SelectSql;
use vendor\main\database\queries\traits\WhereSql;
use vendor\main\database\queries\utils\QueryUtil;
use vendor\main\util\Config;

/**
 * Make queries more easily
 */
class QueryMaker
{
  use WhereSql, SelectSql, OrderSql, JoinsSql;

  private $model,
    $describe = false,
    $insertInto = false,
    $execute = false,
    $updateSet = false,
    $delete = false,
    $joinClass = false,
    $joinCondition = false,
    $counterForJoinClass = 0;

  public function __call($name, $arguments)
  {
    if ($name == 'where') {
      return $this->_where(...$arguments);
    }
    if ($name == 'orderByAsc' || $name == 'orderByDesc') {
      return $this->{"_" . $name}(...$arguments);
    }
  }

  public function isExecute()
  {
    return $this->execute;
  }

  public function isDescribe()
  {
    return $this->describe;
  }

  public function getModel()
  {
    return $this->model;
  }

  public function __construct($selected)
  {
    $this->selected = $selected;
  }

  public static function model($class)
  {
    $n = new static([]);
    $n->model = $class;
    return $n;
  }

  public function describe()
  {
    $this->describe = true;
    return $this;
  }

  public function join($class)
  {
    if (!is_array($this->joinClass))
      $this->joinClass = [];
    $this->joinClass[] = $class;
    return $this;
  }

  public function on($conditions)
  {
    if ($this->joinClass === false)
      return false;

    $el = self::model($this->model);
    $el->counterForJoinClass = $this->counterForJoinClass;
    $el->joinClass = $this->joinClass;

    $conditions($el);

    if (!is_array($this->joinCondition))
      $this->joinCondition = [];

    $this->joinCondition[] = $el->decodeWhereOn();
    $this->counterForJoinClass++;

    return $this;
  }

  public function insertInto($toAdd)
  {
    $toAdd = QueryUtil::allowOnlyFillable($this->model, $toAdd);
    $toAdd = QueryUtil::allowOnlyModelColumns($this->model, $toAdd);
    $toAdd = QueryUtil::sanitize($toAdd);

    if (count($toAdd) === 0)
      return;

    $model = $this->model;

    $q = "INSERT INTO " .(Config::get('app', 'prefix_for_table') ?? '') . $model::$table . "(" . implode(',', array_keys($toAdd)) . ") VALUE (" . implode(',', $toAdd) . ")";
    $this->insertInto = $q;
    $this->execute = true;

    return $this;
  }

  public function decodeWhereOn()
  {
    $q = $this->decodeGivenWhere((array) ($this->wheres), true);
    return $q;
  }

  private function decodeGivenJoin($class, $conditions)
  {
    return "JOIN " . (Config::get('app', 'prefix_for_table') ?? '') .$class::$table . " ON (" . $conditions . ") ";
  }

  public function get()
  {
    if ($this->delete !== false) {
      return $this->delete . "WHERE " . $this->decodeWheres();
    }
    if ($this->describe) {
      return "DESCRIBE " .(Config::get('app', 'prefix_for_table') ?? '') . $this->model::$table;
    }
    if ($this->updateSet !== false) {
      return $this->updateSet . "WHERE " . $this->decodeWheres();
    }
    if ($this->insertInto !== false) {
      // if($this->model == Image::class)
      // dd($this->insertInto);
      return $this->insertInto;
    }

    $q = "SELECT " . $this->decodeSelect() . " FROM " . (Config::get('app', 'prefix_for_table') ?? '') .$this->model::$table . ($this->asFrom !== "" ? " " . $this->asFrom : "");

    if ($this->hasJoins()) {
      $q .= " " . $this->decodeJoins();
    }
    if ($this->hasWheres()) {
      $q .= " WHERE " . $this->decodeWheres();
    }
    if ($this->hasOrders()) {
      $q .= " " . $this->decodeOrders();
    }
    return $q;
  }

  public function updateSet($datas)
  {
    $this->execute = true;
    $q = "UPDATE " .(Config::get('app', 'prefix_for_table') ?? '') . $this->model::$table . " SET ";
    $datas = QueryUtil::sanitize($datas);
    foreach ($datas as $k => $v)
      $q .= "$k=$v, ";
    $this->updateSet = substr($q, 0, strlen($q) - 2) . " ";
    return $this;
  }

  public function delete()
  {
    $this->delete = "DELETE FROM " .(Config::get('app', 'prefix_for_table') ?? '') . $this->model::$table . " ";
    return $this;
  }
}
