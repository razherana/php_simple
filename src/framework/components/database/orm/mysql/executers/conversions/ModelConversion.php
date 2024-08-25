<?php

namespace framework\components\database\orm\mysql\executers\conversions;

use framework\components\database\orm\mysql\executers\conversions\exceptions\ModelConversionException;
use framework\components\database\orm\mysql\executers\MysqlQueryExecuter;
use framework\components\database\orm\mysql\models\instances\DefaultModelInstance;
use framework\components\database\orm\mysql\models\instances\ModelInstance;
use framework\components\database\orm\mysql\models\BaseModel;
use framework\components\database\orm\mysql\models\relations\maps\RelationInfoMap;

/**
 * Contains array_query to Model conversions
 */
class ModelConversion
{
  public static function to_single_model($data, ...$args)
  {
    return self::to_single_model_type($data, ModelInstance::class, ...$args);
  }

  /**
   * @param array $data
   * @param string $type
   */
  public static function to_single_model_type($data, $type, ...$args)
  {
    if (!is_a($type, DefaultModelInstance::class, true)) {
      throw new ModelConversionException("The type given for the conversion isn't a ModelInstance but a '" . $type . "'");
    }
    return new $type($data, ...$args);
  }

  public static function to_single_model_query_type($q, $type, ...$args)
  {
    return self::to_single_model_type(MysqlQueryExecuter::do_clean($q, function ($res) {
      /**
       * @var \mysqli_result $this
       */
      return $res->fetch_array(MYSQLI_ASSOC);
    }), $type, ...$args);
  }

  public static function to_single_model_query($q, ...$args)
  {
    return self::to_single_model_query_type($q, ModelInstance::class, ...$args);
  }

  /**
   * @param array<int|string, array> $datas
   * @param string $type
   */
  public static function to_multiple_model_type($datas, $type, ...$args)
  {
    $all = [];

    foreach ($datas as $k => $v)
      $all[$k] = self::to_single_model_type($v, $type, ...$args);

    return $all;
  }

  /**
   * @param array<int|string, array> $datas
   */
  public static function to_multiple_model($datas, ...$args)
  {
    $all = [];

    foreach ($datas as $k => $v)
      $all[$k] = self::to_single_model_type($v, ModelInstance::class, ...$args);

    return $all;
  }

  /**
   * @param string $q
   * @param string $type
   * @param mixed ...$args
   */
  public static function to_multiple_model_query_type($q, $type, ...$args)
  {
    $all = MysqlQueryExecuter::do_clean(
      $q,
      function ($res) {
        /**
         * @var \mysqli_result $res
         */
        return $res->fetch_all(MYSQLI_ASSOC);
      }
    );

    return self::to_multiple_model_type($all, $type, ...$args);
  }

  /**
   * Returns an array of ModelInstance with relation set
   * @param array[] $all
   * @param BaseModel $parent_model
   */
  public static function to_array_models_relation($all, $parent_model)
  {
    if (is_array($all) && count($all) <= 0) return [];

    /** @var array<int|string, ModelInstance> $result */
    $result = [];

    $relations = $parent_model->relation_info_maps;
    $keys_main_model = $parent_model->get_keys();
    $prim_key = $parent_model::$primary_key;

    foreach ($all as $row) {
      if (!isset($result[$row[$prim_key]])) {
        $main_model_data = [];

        foreach ($keys_main_model as $k)
          if (isset($row[$k])) // OPTIMISE HERE
            $main_model_data[$k] = $row[$k];

        $main_model = self::to_single_model($main_model_data, $parent_model::class);
        $result[$row[$prim_key]] = $main_model;
      } else {
        $main_model = $result[$row[$prim_key]];
      }

      foreach ($relations as $rel_name => $relation) {
        if (!isset($main_model->relations[$rel_name]))
          $main_model->relations[$rel_name] = [];

        /** 
         * This will work because the $primary_key 
         * is different from each row 
         */
        $temp_relation = self::load_relation_row($relation, $row);

        if (count($temp_relation) <= 0) continue;

        if (!isset($main_model->relations[$rel_name][array_key_first($temp_relation)]))
          switch ($relation->type) {
            case RelationInfoMap::HAS_MANY:
              $main_model->relations[$rel_name] += $temp_relation;
              break;
            case RelationInfoMap::ONE_TO_ONE:
            case RelationInfoMap::BELONGS_TO:
              $content = array_values($temp_relation);

              if (count($content) <= 0) $content = null;
              else $content[array_key_first($content)];

              $main_model->relations[$rel_name] = $content;
              break;
          }
      }
    }

    return $result;
  }

  /**
   * Load the row into a model and it's relations
   * @param RelationInfoMap $relation
   * @param array<string, string|int|float|null> $row
   */
  protected static function load_relation_row($relation, $row)
  {
    $keys = $relation->get_keys();
    $model_name = $relation->data['model'];

    $primary_key_value = $row[$relation->uniq_id . "_0_" . $relation->get_primary_key()];

    if ($primary_key_value === null) return [];

    $model_data = [];

    foreach ($keys as $o => $r) {
      if (array_key_exists($r, $row))
        $model_data[$o] = $row[$r];
    }

    /** @var ModelInstance $model */
    $model = self::to_single_model($model_data, $model_name);

    if (!empty($relation->relations))
      foreach ($relation->relations as $model_name => $ens_rel)
        foreach ($ens_rel as $rel_name => $rel) {

          if (
            !isset($model->relations[$model_name][$rel_name])
            && $relation->type == RelationInfoMap::HAS_MANY
          )
            $model->relations[$model_name][$rel_name] = [];

          /** 
           * This will work because the $primary_key 
           * is different from each row 
           */
          $temp_relation = self::load_relation_row($rel, $row);

          if (count($temp_relation) <= 0) continue;

          if (!isset($model->relations[$model_name][$rel_name][array_key_first($temp_relation)]))
            switch ($relation->type) {
              case RelationInfoMap::HAS_MANY:
                $model->relations[$model_name][$rel_name] += $temp_relation;
                break;
              case RelationInfoMap::ONE_TO_ONE:
              case RelationInfoMap::BELONGS_TO:
                $content = array_values($temp_relation);

                if (count($content) <= 0) $content = null;
                else $content[array_key_first($content)];

                $model->relations[$model_name][$rel_name] = $content;
                break;
            }
        }

    return [$primary_key_value => $model];
  }
}
