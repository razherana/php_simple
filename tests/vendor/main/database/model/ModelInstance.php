<?php

namespace vendor\main\database\model;

use vendor\main\database\model\relations\Relation;
use vendor\main\database\queries\QueryMaker;
use vendor\main\database\queries\QueryResult;

final class ModelInstance
{
  protected $attributes = [], $originalAttributes = [];
  public $relations = [];
  public $modelName = "";
  private static $relationsKey = [
    Relation::hasMany => "hasMany", Relation::belongsTo => "belongsTo"
  ];

  public function getAttributes()
  {
    return $this->attributes;
  }

  public function __construct($modelName, $attributes)
  {
    $this->attributes = $attributes;
    $this->originalAttributes = $attributes;
    $this->modelName = $modelName;
  }

  public function __set($name, $value)
  {
    if (array_key_exists($name, $this->attributes))
      $this->attributes[$name] = $value;
    if (method_exists($this->modelName, $name)) {
      $obj = new $this->modelName;
      $rel = $obj->{$name}();

      if (isset($this->relations[$rel->modelName][$name])) {
        $this->relations[$rel->modelName][$name] = $value;
      }
    }
  }

  public function __get($name)
  {
    if (array_key_exists($name, $this->attributes)) {
      if (in_array($name, $this->modelName::$hidden))
        return false;
      return $this->attributes[$name];
    }
    if (method_exists($this->modelName, $name)) {
      $obj = new $this->modelName;
      $rel = $obj->{$name}();

      if (isset($this->relations[$rel->modelName][$name])) {
        return $this->relations[$rel->modelName][$name];
      }

      return $this->{self::$relationsKey[$rel['type']]}([$rel['modelName'], $rel['my_id'], $rel['other_id']]);
    }
    return null;
  }

  public function delete()
  {
    $q = QueryMaker::model($this->modelName)->delete();
    $i = false;
    foreach ($this->originalAttributes as $k => $v) {
      if ($i) {
        $q = $q->andWhere($k, '=', $v);
        continue;
      }
      $q = $q->where($k, '=', $v);
      $i = true;
    }
    return QueryResult::execute($q);
  }

  public function save()
  {
    $q = QueryMaker::model($this->modelName)->updateSet($this->attributes);
    $i = false;
    foreach ($this->originalAttributes as $k => $v) {
      if ($i) {
        $q = $q->andWhere($k, '=', $v);
        continue;
      }
      $q = $q->where($k, '=', $v);
      $i = true;
    }
    return QueryResult::execute($q);
  }

  // Relations

  /**
   * hasMany relations
   * this -> [
   *  other1,
   *  other2,
   *  other3
   * ]
   */
  public function hasMany($ens)
  {
    $other_class = $ens[0];
    $my_id = $ens[1];
    $other_id = $ens[2];

    return $other_class::where($other_id, '=', $this->{$my_id})->get();
  }

  /**
   * belongsTo Relation
   * 
   */
  public function belongsTo($ens)
  {
    $other_class = $ens[0];
    $my_id = $ens[1];
    $other_id = $ens[2];

    $ret = $other_class::where($other_id, '=', $this->{$my_id})->get();
    return ($ret[array_key_first($ret)]) ?? null;
  }
}
