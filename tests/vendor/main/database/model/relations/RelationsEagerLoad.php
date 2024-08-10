<?php

namespace vendor\main\database\model\relations;

use app\Models\File;
use app\Models\User;
use vendor\main\database\queries\QueryMaker;
use vendor\main\util\Arrays;

trait RelationsEagerLoad
{
  /**
   * relation1 : [
   *  "type" => int,
   *  "relationModel" => model::class,
   *  "my_id" => id_name,
   *  "other_id" => id_name
   * ]
   */
  public $relations = [];

  public $relationMap = [];

  /**
   * Pre-load relations
   */
  protected function _relations($ens)
  {
    foreach ($ens as $relation) {

      $rel = $this->{$relation}();
      $rel->relationName = $relation;

      if (!empty(($rel->modelName)::$with)) {

        $other = ($rel->modelName)::preLoadWiths();


        $rels = $other->relations;
        $rel->relations = array_merge_recursive($rels, $rel->relations);
      }

      $this->relationMap[] = RelationMap::make($rel->motherModelName, $rel->modelName, $rel->type, $rel);
      $this->relations[] = $rel;
    }
    return $this;
  }

  /**
   * Pre-load relations
   */
  protected static function relationsStatic($ens, $use = null)
  {
    if (is_string($ens)) $ens = [$ens];
    $e = new static($use);
    $e->_relations($ens);
    return $e;
  }

  /**
   * Defines a new hasMany relation
   */
  public function hasMany($relationModel, $my_id, $other_id)
  {
    $rel = new Relation(Relation::hasMany, $relationModel, $my_id, $other_id, static::class);
    return $rel;
  }

  /**
   * Defines a new belongsTo relation
   */
  public function belongsTo($relationModel, $my_id, $other_id)
  {
    $rel = new Relation(Relation::belongsTo, $relationModel, $my_id, $other_id, static::class);
    return $rel;
  }

  /**
   * Get the query to get the relations
   */
  public function getRelationQuery($query)
  {
    foreach ($this->relations as $rel) {
      $q = $rel->getQuery($query);
      if ($rel->type == Relation::hasMany) {
        $query->leftJoinFromQuery($q->get(), $rel->my_id, $rel->other_id, $rel['UUID'])->addSelect(array_values($q->selected));
      } else if ($rel->type == Relation::belongsTo) {
        $query->rightJoinFromQuery($q->get(), $rel->my_id, $rel->other_id, $rel['UUID'])->addSelect(array_values($q->selected));
      }
      $query->selected = array_merge(array_unique(array_merge($query->selected, array_values($q->selected))));
    }
    return $query;
  }

  /**
   * Check if the Model has a relation
   */
  public function hasRelation()
  {
    return !empty($this->relations);
  }
}
