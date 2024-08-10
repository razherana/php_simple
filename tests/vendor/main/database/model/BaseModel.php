<?php

namespace vendor\main\database\model;

use vendor\main\database\model\relations\RelationsEagerLoad;
use vendor\main\database\queries\QueryMaker;
use vendor\main\database\queries\QueryResult;
use vendor\main\database\queries\traits\OrderSql;
use vendor\main\database\queries\traits\SelectSql;
use vendor\main\database\queries\traits\WhereSql;

class BaseModel
{
  use RelationsEagerLoad, WhereSql, OrderSql, SelectSql;
  /**
   * Table name
   */
  public static $table = "";

  /**
   * Fillable in the Model
   */
  public static $fillable = [];

  /**
   * Primary Key of the Model
   */
  public static $primaryKey = "id";

  /**
   * Hidden in the result
   */
  public static $hidden = [];

  /**
   * Relations to auto use
   */
  public static $with = [];

  public function __construct()
  {
  }

  public static function __callStatic($name, $arguments)
  {
    if ($name == 'where') {
      $a = static::preLoadWiths();
      return $a->where(...$arguments);
    }
    if ($name == 'orderByAsc' || $name == 'orderByDesc') {
      $a = static::preLoadWiths();
      return $a->$name(...$arguments);
    }
    if ($name == 'relations') {
      $a = static::preLoadWiths();
      return $a->relations(...$arguments);
    }
  }

  public function __call($name, $arguments)
  {
    if ($name == 'where') {
      return $this->_where(...$arguments);
    }
    if ($name == 'orderByAsc' || $name == 'orderByDesc') {
      return $this->{"_" . $name}(...$arguments);
    }
    if ($name == 'relations') {
      return $this->_relations(...$arguments);
    }
  }

  public static function getAttributeInfo()
  {
    return QueryResult::getArray(QueryMaker::model(static::class)->describe());
  }

  public static function all($select, $prefix = true)
  {
    $a = static::preLoadWiths();
    return $a->select($select, $prefix)->get();
  }

  public static function create($data)
  {
    return QueryResult::execute(QueryMaker::model(static::class)->insertInto($data));
  }

  public static function find($id)
  {
    $var = static::preLoadWiths();
    $res = $var->where(static::$primaryKey, '=', $id)->get();
    return count($res) > 0 ? $res[0] : null;
  }

  public static function clear()
  {
    return QueryResult::execute(QueryMaker::model(static::class)->delete()->where("1", '=', 1));
  }

  public static function delete($id)
  {
    return QueryResult::execute(QueryMaker::model(static::class)->delete()->where(static::$primaryKey, '=', $id));
  }

  protected static function preLoadWiths()
  {
    $a = new static();
    return $a->relations(static::$with);
  }

  public function get()
  {
    $main_query = QueryMaker::model(static::class);
    $relation = false;

    if ($this->hasSelect()) {
      $main_query->select($this->selected);
    } else {
      $main_query->select(static::class);
    }

    if ($this->hasWheres()) {
      $main_query->wheres = $this->wheres;
    }

    if ($this->hasRelation()) {
      $main_query = $this->getRelationQuery($main_query);
      $relation = true;
    }

    if ($this->hasOrders()) {
      $main_query->orders = $this->orders;
    }

    if ($relation) {
      return QueryResult::getArray($main_query, null, $this->relations);
    }
    return QueryResult::getArray($main_query);
  }
}
