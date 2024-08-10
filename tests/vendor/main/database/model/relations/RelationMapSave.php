<?php

namespace vendor\main\database\model\relations;

use vendor\main\database\model\ModelInstance;

class RelationMapSave
{
  public $relations = [];
  public $relation;
  public $model;

  public function __construct($relation, $model)
  {
    $this->relation = $relation;
    $this->model = $model;
  }

  public function toRelation() {
    $relations = [];
    
    if(!empty($this->relations)) {

    }
    $relations[$this->relation->modelName][$this->relation->relationName] = $this->model;
  }
}
