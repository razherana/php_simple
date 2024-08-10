<?php

namespace vendor\main\conversions;

use vendor\main\database\model\ModelInstance;
use vendor\main\database\model\relations\Relation;
use vendor\main\Facades;
use vendor\main\util\Arrays;

class ToModel extends Facades
{
  public static $functions = [
    "make" => "toModel",
    "makeAll" => "toModelAll",
    "makeSpe" => "toModelSpe"
  ];

  public function toModel($all)
  {
    $att = $all[0];
    $modelName = $all[1];
    return new ModelInstance($modelName, $att);
  }

  public function toModelAll($all)
  {
    $els = $all[0];
    $modelName = $all[1];
    $res = [];
    foreach ($els as $v) {
      $res[] = static::make($v, $modelName);
    }
    return $res;
  }

  public function toModelSpe($all)
  {
    $att = $all[0];
    $modelName = $all[1];
    $spe = $all[2];
    return new $spe($att, $modelName);
  }

  /**
   * @param array $row
   * @param Relation[] $relations
   */
  private static function getAllModelRelations($relationsEns, $row, $relations, $parent)
  {
    foreach ($relations as $relation) {

      if (!empty($relation->relations)) {
        if (!isset($relationsEns[$relation->modelName])) {
          $relationsEns[$relation->modelName] = [];
        }
        if (!isset($relationsEns[$relation->modelName]['relations___'])) {
          $relationsEns[$relation->modelName]['relations___'] = [];
        }
        $aba = self::getAllModelRelations($relationsEns[$relation->modelName]['relations___'], $row, $relation->relations, $relation['UUID'] . "." . $relation->modelName::$primaryKey);

        $relationsEns[$relation->modelName]['relations___'] = array_merge_recursive($aba, $relationsEns[$relation->modelName]['relations___']);
      }

      $a = self::toModelRelation($row, $relation);
      if ($row[$parent] != NULL) {
        if ($relation->type == Relation::hasMany)
          $relationsEns[$relation->modelName][$relation->relationName][$ababa ?? $row[$parent]][] = $a;
        else if ($relation->type == Relation::belongsTo)
          $relationsEns[$relation->modelName][$relation->relationName][$ababa ?? $row[$parent]] = $a;
      }
    }
    return $relationsEns;
  }

  private static function toModelRelation($row, $relation)
  {
    $attributes_name = $relation->attributeInfo->getAttributeName();
    $attributes_name_id = $relation->attributes;
    $new_attributes = [];

    foreach ($attributes_name as $k => $att)
      $new_attributes[$att] = $row[$attributes_name_id[$k]];

    return new ModelInstance($relation['modelName'], $new_attributes);
  }

  private static function reArrangeModelsWithRelations($relationsEns)
  {
    foreach ($relationsEns as &$array_of_relations) {

      if (!empty($array_of_relations['relations___'])) {
        $array_of_relations['relations___'] = self::reArrangeModelsWithRelations($array_of_relations['relations___']);


        foreach ($array_of_relations['relations___'] as $modelName1 => $modelArray)
          foreach ($modelArray as $r1 => $a1) {
            foreach ($a1 as $pKey => $rel_arr) {

              // Add relations to each models
              foreach ($array_of_relations as $to_pass_relations => &$val) {
                if ($to_pass_relations == 'relations___') continue;

                foreach ($val as &$v11)
                  if (is_array($v11)) foreach ($v11 as &$v) {
                    if ($v->{$v->modelName::$primaryKey} == $pKey)
                      $v->relations[$modelName1][$r1] = $rel_arr;
                  } else {
                    if ($v11->{$v11->modelName::$primaryKey} == $pKey)
                      $v11->relations[$modelName1][$r1] = $rel_arr;
                  }
              }
            }
          }
      }
    }

    return $relationsEns;
  }

  public static function toModelWithRelations($all, $relations, $modelName)
  {
    $models = [];
    $primaryKey = $modelName::$primaryKey;
    $attributeInfo = $modelName::getAttributeInfo();

    $attributes_name = $attributeInfo->getAttributeName();

    $relationsEns = [];

    foreach ($all as $row) {
      $relationsEns = self::getAllModelRelations($relationsEns, $row, $relations, $modelName::$primaryKey);

      $new_attributes = [];

      foreach ($attributes_name as $name)
        $new_attributes[$name] = $row[$name];

      if ($row[$primaryKey] == null) continue;
      $a = new ModelInstance($modelName, $new_attributes);
      $models[$row[$primaryKey]] = $a;
    }

    $val = self::reArrangeModelsWithRelations($relationsEns);

    foreach ($val as $modelName => $rel)
      foreach ($rel as $rel_name => $arr) {
        if ($rel_name == 'relations___') continue;
        foreach ($arr as $k => $v)
          $models[$k]->relations[$modelName][$rel_name] = $v;
      }

    return array_merge($models);
  }
}
