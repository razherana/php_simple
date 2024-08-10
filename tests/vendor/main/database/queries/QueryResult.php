<?php

namespace vendor\main\database\queries;

use vendor\main\database\connection\Database;
use vendor\main\conversions\ToModel;
use vendor\main\database\model\DescribedTable;
use vendor\main\Facades;

class QueryResult extends Facades
{
  private $q, $fromModel = "", $describe, $onlyQuery = false;

  public static $functions = [
    "getArray" => "getArrayFacade",
    "execute" => "executeFacade"
  ];

  public function __construct($query = null, $model = null)
  {
    if ($query instanceof QueryMaker) {
      $this->q = $query->get();

      if (is_null($model)) {
        $this->fromModel = $query->getModel();
      } else {
        $this->fromModel = $model;
      }

      $this->describe = $query->isDescribe();
    } else if ($query != null) {
      $this->q = $query;
      $this->onlyQuery = true;
    }
  }

  public function getArrayWithRelations($relationMap)
  {
    $res = mysqli_query(Database::get(), $this->q);
    $v = mysqli_fetch_all($res, MYSQLI_ASSOC);
    mysqli_free_result($res);
    return ToModel::toModelWithRelations($v, $relationMap, $this->fromModel);
  }

  public function getArrayy()
  {
    $res = mysqli_query(Database::get(), $this->q);
    $v = mysqli_fetch_all($res, MYSQLI_ASSOC);
    mysqli_free_result($res);
    if ($this->onlyQuery)
      return $v;
    if ($this->fromModel != "")
      return ToModel::makeAll($v, $this->fromModel);
  }

  public function getDescribedElement()
  {
    $res = mysqli_query(Database::get(), $this->q);
    $v = mysqli_fetch_all($res, MYSQLI_ASSOC);
    mysqli_free_result($res);
    return ToModel::makeSpe($v, $this->fromModel, DescribedTable::class);
  }

  public function getArrayFacade($q)
  {
    if (!array_key_exists(1, $q)) {
      $q[1] = null;
    }
    $a = new static($q[0], $q[1]);

    if ($a->describe) {
      return $a->getDescribedElement();
    }

    if (isset($q[2])) {
      return $a->getArrayWithRelations($q[2]);
    }

    return $a->getArrayy();
  }

  public function executeFacade($q)
  {
    $a = new static($q[0]);
    $res = mysqli_query(Database::get(), $a->q);
    return $res !== null;
  }
}
