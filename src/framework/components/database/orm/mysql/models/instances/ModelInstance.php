<?php

namespace framework\components\database\orm\mysql\models\instances;

use framework\components\database\orm\mysql\models\BaseModel;
use framework\components\database\orm\mysql\models\instances\traits\ModelInstanceTrait;

class ModelInstance extends DefaultModelInstance
{
  use ModelInstanceTrait;

  /**
   * Contains Model::class or Model if possible
   * @var BaseModel|string $parent_model
   */
  public $parent_model;

  /**
   * Contains relation elements
   */
  public $relations = [];

  /**
   * @param array $attributes
   * @param BaseModel|string $parent_model
   */
  public function __construct($attributes, $parent_model = null)
  {
    parent::__construct($attributes);
    $this->parent_model = $parent_model;
  }
}
