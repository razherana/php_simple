<?php

namespace framework\components\database\orm\mysql\models\instances;

use framework\components\database\orm\mysql\models\BaseModel;
use framework\components\database\orm\mysql\models\instances\traits\ModelInstanceTrait;
use framework\components\database\orm\mysql\models\instances\traits\ModelRelationAccessTrait;

class ModelInstance extends DefaultModelInstance
{
  use ModelInstanceTrait, ModelRelationAccessTrait;

  /**
   * Contains Model::class or Model if possible
   * @var BaseModel|string $parent_model
   */
  public $parent_model;

  /**
   * @param array $attributes
   * @param BaseModel|string $parent_model
   */
  public function __construct($attributes, $parent_model = null)
  {
    parent::__construct($attributes);
    $this->parent_model = $parent_model;
  }

  /**
   * Main __get magic method
   */
  public function __get($name)
  {
    // Get the relation
    if (($relation = $this->relation_access($name)) !== false) {
      return $relation;
    }
    return parent::__get($name);
  }
}
