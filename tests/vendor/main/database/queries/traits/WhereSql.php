<?php

namespace vendor\main\database\queries\traits;

use vendor\main\database\queries\utils\QueryUtil;
use vendor\main\util\Config;

/**
 * If you want to add static where, add in the __callStatic where => whereStatic
 */
trait WhereSql
{
  public $wheres = [];

  public function hasWheres()
  {
    return !empty($this->wheres);
  }

  public static function whereStatic($key, $operator, $value, $repair = true, $use = null)
  {
    $el = is_null($use) ? new static() : new static($use);
    $el->_where($key, $operator, $value, $repair);
    return $el;
  }

  /**
   * Only when doing the first where
   */
  public function _where($key, $operator, $value, $repair = true)
  {
    if ($repair)
      $value = QueryUtil::repairValue($value);
    if ($value === "NULL") {
      if ($operator == '=') $operator = "IS";
      else if ($operator == '!=') $operator = "IS NOT";
    }
    $where = [$key, $operator, $value];
    $this->wheres = [$where];
    return $this;
  }

  public function andWhere($key, $operator, $value, $repair = true)
  {
    if ($repair)
      $value = QueryUtil::repairValue($value);
    if ($value === "NULL") {
      if ($operator == '=') $operator = "IS";
      else if ($operator == '!=') $operator = "IS NOT";
    }
    $where = [$key, $operator, $value];
    array_push($this->wheres, "AND");
    array_push($this->wheres, $where);
    return $this;
  }

  public function orWhere($key, $operator, $value, $repair = true)
  {
    if ($repair)
      $value = QueryUtil::repairValue($value);
    if ($value === "NULL") {
      if ($operator == '=') $operator = "IS";
      else if ($operator == '!=') $operator = "IS NOT";
    }
    $where = [$key, $operator, $value];
    array_push($this->wheres, "OR");
    array_push($this->wheres, $where);
    return $this;
  }

  /**
   * groupWhere(function($e){ 
   * $e->where()
   * });
   * @param string $mode : "AND" | "OR"
   */
  private function groupWhereAll($function, $mode)
  {
    $new = new static([]);
    $function($new);
    if ($mode !== false)
      $this->wheres[] = $mode;
    $this->wheres[] = $new->wheres;
    return $this;
  }

  public function andGroupWhere($function)
  {
    return $this->groupWhereAll($function, "AND");
  }

  public function orGroupWhere($function)
  {
    return $this->groupWhereAll($function, "OR");
  }

  public function groupWhere($function)
  {
    return $this->groupWhereAll($function, false);
  }

  protected function decodeGivenWhere($data, $join = false)
  {
    $q = "";
    foreach ($data as $v) {
      if (is_string($v)) {
        $q .= $v . " ";
      } else if (is_array($v) && count($v) == 3 && is_string($v[0]) && is_string($v[1])) {
        if ($join === false)
          $q .= implode(" ", $v) . " ";
        else $q .= (is_numeric($v[0]) ? self::repairValue($v[0]) : ((Config::get('app', 'prefix_for_table') ?? '') . $this->model::$table . "." . $v[0])) . " " . $v[1] . " " . (is_numeric($v[2]) ? self::repairValue($v[2]) : ((Config::get('app', 'prefix_for_table') ?? '') . $this->joinClass[$this->counterForJoinClass]::$table . ".") . $v[2]) . " ";
      } else {
        $q .= "(" . $this->decodeGivenWhere($v, $join) . ")";
      }
    }
    return $q;
  }

  protected function decodeWheres()
  {
    $q = $this->decodeGivenWhere($this->wheres);
    return $q;
  }
}
