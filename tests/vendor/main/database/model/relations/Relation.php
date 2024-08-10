<?php

namespace vendor\main\database\model\relations;

use ArrayAccess;
use vendor\main\database\model\DescribedTable;
use vendor\main\database\queries\QueryMaker;
use vendor\main\util\Config;

/**
 * To store relations and order them better
 */
class Relation implements ArrayAccess
{
  public const hasMany = 0;
  public const belongsTo = 1;

  public $type = 0;
  public $modelName = "";
  public $motherModelName = "";
  public $my_id = "";
  public $other_id = "";
  public $relationName = "";
  protected $UUID = "";
  /**
   * @var string[] $attributes
   */
  public $attributes = [];
  public $attributeInfo = null;

  /**
   * Relations
   * @var Relation[]
   */
  public $relations = [];

  public function offsetSet($offset, $value): void
  {
    if ($this->offsetExists($offset)) {
      if ($offset != "UUID")
        $this->{$offset} = $value;
    } else
      throw new \OutOfRangeException("The key `$offset` doesn't exist");
  }

  public function offsetUnset($offset): void
  {
    if ($this->offsetExists($offset)) {
      if ($offset != "UUID")
        $this->{$offset} = null;
    } else
      throw new \OutOfRangeException("The key $offset doesn't exist");
  }

  public function offsetExists($offset): bool
  {
    return in_array($offset, ['type', 'modelName', 'my_id', 'other_id', 'UUID']);
  }

  public function offsetGet($offset): mixed
  {
    if (!in_array($offset, ['type', 'modelName', 'my_id', 'other_id', 'UUID'])) {
      throw new \OutOfRangeException("The key $offset doesn't exist");
    }
    return $this->{$offset};
  }

  public function __construct($type, $modelName, $my_id, $other_id, $motherModelName)
  {
    $this->motherModelName = $motherModelName;
    $this->type = $type;
    $this->modelName = $modelName;
    $this->my_id = $my_id;
    $this->other_id = $other_id;
    $this->UUID = bin2hex(random_bytes(5));
    $this->attributeInfo = $this->modelName::getAttributeInfo();

    foreach ($this->attributeInfo->getAttributeName() as $att)
      $this->attributes[] = $this->UUID . ".$att";
  }

  public function getSelect()
  {
    $uuid = $this->UUID;
    $new_attr = [];
    if (!empty($this->relations)) {
      foreach ($this->relations as $rel) {
        $new_attr += $rel->getSelect();
      }
    }
    $attributes = $this->attributeInfo->getAttributeName();

    foreach ($attributes as $att)
      $new_attr[] = '`' . $uuid . '`.' . $att;

    return $new_attr;
  }

  public function getJoin($as1 = null)
  {
    if (!empty($this->relations)) {
      $attributes = $this->attributeInfo->getAttributeName();

      $new_attr = [];

      foreach ($attributes as $att)
        $new_attr["`" . $this->UUID . "`.$att"] = '`' . $this->UUID . ".$att`";

      $query = QueryMaker::model($this->modelName)->asFrom($this->UUID)->addSelect($new_attr);
      // debug
      // if(count($this->relations) == 2) {

      // }
      foreach ($this->relations as $relation) {
        if ($relation->type == Relation::hasMany) {
          $aba = $relation->getJoin($this->UUID);
          $query = $query->leftJoinFromQuery($aba->get(),  '`' . $this->UUID . '`.' . $relation->my_id, $relation->other_id, $relation->UUID)->addSelect(array_values($aba->selected));
        } else if ($relation->type == Relation::belongsTo) {
          $aba = $relation->getJoin($this->UUID);
          $query->rightJoinFromQuery($aba->get(), '`' . $this->UUID . '`.' . $relation->my_id, $relation->other_id, $relation->UUID)->addSelect(array_values($aba->selected));
        }
      }
      return $query;
    } else {
      $attributes = $this->attributeInfo->getAttributeName();

      $new_attr = [];

      foreach ($attributes as $att)
        $new_attr[(Config::get('app', 'prefix_for_table') ?? '') . $this->modelName::$table . ".$att"] = '`' . $this->UUID . ".$att`";

      $q = QueryMaker::model($this->modelName)->select($new_attr);
      return $q;
      // $attributes = $this->attributeInfo->getAttributeName();

      // foreach ($attributes as $att)
      //   $new_attr[$att] = '`' . $this->UUID . "`.$att";

      // $param = array($this->modelName, $this->my_id, $this->other_id, $this->UUID, $as1);

      // return ($this->type == Relation::hasMany ? $q->leftJoin(...$param)->select($new_attr) : $q->rightJoin(...$param)->select($new_attr));
    }
  }


  public function getJoin2($query, $as1)
  {
    if (!empty($this->relations)) {
      foreach ($this->relations as $k => $relation) {
        if ($this->type == Relation::hasMany)
          $query = $query->leftJoinFromQuery($relation->getJoin($query, $this->UUID)->get(), $this->my_id, $this->other_id, $relation->UUID);
        else if ($this->type == Relation::belongsTo)
          $query = $query->rightJoinFromQuery($relation->getJoin($query, $this->UUID)->get(), $this->my_id, $this->other_id, $relation->UUID);
      }
    } else {
      $attributes = $this->attributeInfo->getAttributeName();

      foreach ($attributes as $att)
        $new_attr[$att] = '`' . $this->UUID . '`.' . $att;

      $param = array($this->modelName, $this->my_id, $this->other_id, $this->UUID, $as1);

      return ($this->type == Relation::hasMany ? $query->leftJoin(...$param)->addSelect($new_attr) : $query->rightJoin(...$param)->addSelect($new_attr));
    }
    return $query;
  }

  /**
   * Why $q_LM10 ?
   * Because Lionel Messi 10 is the goat
   */
  public function getQuery($q_LM10)
  {
    $q_LM10 = $this->getJoin($this->UUID);
    return $q_LM10;
  }
}
