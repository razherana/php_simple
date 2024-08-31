<?php

namespace framework\components\database\orm\mysql\models\instances\traits;

use framework\components\database\orm\mysql\models\relations\maps\RelationInfoMap;

trait ModelRelationAccessTrait
{
  /**
   * Contains relation elements
   */
  public $relations = [];

  protected function relation_access($relation_name)
  {
    // Checks eager load relation
    if (isset($this->relations[$relation_name])) {
      return $this->relations[$relation_name];
    }

    // Checks lazy load relation
    if (method_exists($this->parent_model, $relation_name)) {
      $relation = (new $this->parent_model)->$relation_name();

      if ($relation instanceof RelationInfoMap) {
        $key_value = $this->{$relation->data['my_id']};
        $result = $relation->get_lazy($key_value);
        $return = null;

        switch ($relation->type) {
          case RelationInfoMap::BELONGS_TO:
          case RelationInfoMap::ONE_TO_ONE:
            $return = $result[array_key_first($result)] ?? null;
            break;
          case RelationInfoMap::HAS_MANY:
            $return = $result;
            break;
        }

        $this->relations[$relation_name] = $return;
        return $return;
      }
    }
    return false;
  }
}
