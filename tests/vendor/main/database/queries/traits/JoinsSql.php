<?php

namespace vendor\main\database\queries\traits;

use vendor\main\database\model\BaseModel;
use vendor\main\database\queries\QueryMaker;
use vendor\main\util\Config;

/**
 * Joins in sql :
 * "leftJoin",
 * "rightJoin",
 * "join"
 */
trait JoinsSql
{
  public $joins = [];

  private static $consts = [
    "leftJoin" => "LEFT JOIN",
    "rightJoin" => "RIGHT JOIN",
    "join" => "JOIN",
    "innerJoin" => "INNER JOIN"
  ];

  public function leftJoinFromQuery($queryMaker, $my_id, $other_id, $as1, $as2 = "")
  {
    $this->joins[] = [$queryMaker, $my_id, $other_id, "leftJoin", $as1, $as2];
    return $this;
  }

  public function rightJoinFromQuery($queryMaker, $my_id, $other_id, $as1, $as2 = "")
  {
    $this->joins[] = [$queryMaker, $my_id, $other_id, "rightJoin", $as1, $as2];
    return $this;
  }

  public function innerJoinFromQuery($queryMaker, $my_id, $other_id, $as1, $as2 = "")
  {
    $this->joins[] = [$queryMaker, $my_id, $other_id, "innerJoin", $as1, $as2];
    return $this;
  }

  public function innerJoin($modelName, $my_id, $other_id, $as1 = null, $as2 = null)
  {
    $this->joins[] = [$modelName, $my_id, $other_id, "innerJoin", $as1, $as2];
    return $this;
  }

  public function leftJoin($modelName, $my_id, $other_id, $as1 = null, $as2 = null)
  {
    $this->joins[] = [$modelName, $my_id, $other_id, "leftJoin", $as1, $as2];
    return $this;
  }

  public function rightJoin($modelName, $my_id, $other_id, $as1 = null, $as2 = null)
  {
    $this->joins[] = [$modelName, $my_id, $other_id, "rightJoin", $as1, $as2];
    return $this;
  }

  public function join($modelName, $my_id, $other_id, $as1 = null, $as2 = null)
  {
    $this->joins[] = [$modelName, $my_id, $other_id, "join", $as1, $as2];
    return $this;
  }

  public function decodeJoins()
  {
    $sss = "";
    foreach ($this->joins as $join) {
      if (is_a($join[0], BaseModel::class, true)) {
        $sss .= self::$consts[$join[3]] . " " .(Config::get('app', 'prefix_for_table') ?? '') . $join[0]::$table . (!empty($join[5]) ? " AS " . $join[5] : "") . " ON "  . (!empty($join[4]) ? $join[4] :(Config::get('app', 'prefix_for_table') ?? '') . $this->model::$table) . "." . $join[1] . "=" . (!empty($join[5]) ? $join[5] : (Config::get('app', 'prefix_for_table') ?? '') .$join[0]::$table) . "." . $join[2] . " ";
      } else {
        $sss .= self::$consts[$join[3]] . " (" . $join[0] . ") AS `" . $join[4] . "` ON "  . (!empty($join[5]) ? ("`" . $join[5] . "`.") : "") . $join[1] . "=`" . $join[4] . "." . $join[2] . "` ";
      }
    }
    return $sss;
  }

  public function hasJoins()
  {
    return !empty($this->joins);
  }
}
