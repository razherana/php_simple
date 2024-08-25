<?php

namespace framework\components\database\orm\mysql\models\relations\maps;

use framework\components\database\orm\mysql\models\BaseModel;
use framework\components\database\orm\mysql\models\relations\exceptions\RelationDefinitionException;
use framework\components\database\orm\mysql\queries\DefaultQueryMaker;
use framework\components\database\orm\mysql\queries\SortedQueryMaker;
use framework\components\database\orm\mysql\request\elements\Join;
use framework\components\database\orm\mysql\request\elements\Select;
use framework\components\database\orm\mysql\request\elements\Where;
use framework\components\database\orm\mysql\request\MysqlQueryable;

/**
 * Contains all of the innformation on the relation and models
 */
class RelationInfoMap
{
  public const HAS_MANY = 0, BELONGS_TO = 1, ONE_TO_ONE = 2;

  /**
   * Contains the key's name on the mysql_result
   * "original_key_name" => "row_key_name"
   * @var ?array<string, string> $keys
   */
  protected $keys = null;

  /**
   * Contains the primary key for the model
   * relation
   * @var ?string $primary_key
   */
  protected $primary_key = null;

  public const TYPES_TO_JOIN = [
    self::HAS_MANY => Join::LEFT,
    self::BELONGS_TO => Join::RIGHT,
    self::ONE_TO_ONE => Join::INNER,
  ];

  /**
   * Contains the parent model
   * @var BaseModel $parent_model
   */
  public $parent_model;

  /**
   * Contains the parent relation
   * @var ?self $parent_relation
   */
  public $parent_relation = null;

  /**
   * Unique ID for this RelationInfoMap
   * @var string $uniq_id
   */
  public $uniq_id = '';

  /**
   * @var int $type
   */
  public $type;

  /**
   * Contains needed data for the joins after
   * @var array $data
   */
  public $data = [];

  /**
   * Contains the relations of other models
   * @var array<string,
   *  array<
   *    string,
   *    self
   *    >
   *  > $relations
   */
  public $relations;

  /**
   * Creates a relation map, 
   * and if needed will add all of the eager load relations
   * @param BaseModel $parent_model
   * @param int $type
   * @param array<string, mixed> $data
   * @param bool $add_eager_load
   */
  public function __construct($parent_model, $type, $data, $add_eager_load = true)
  {
    $this->parent_model = $parent_model;
    $this->type = $type;
    $this->data = $data;
    $this->uniq_id = uniqid("r");

    if (!isset($data['model']) || !is_a($data['model'], BaseModel::class, true)) {
      throw new RelationDefinitionException("The model given is not a BaseModel child (" . $data['model'] . ")");
    }

    if ($add_eager_load === true) {
      $this->add_eager_load();
    }
  }

  public function get_keys()
  {
    if (!is_null($this->keys)) return $this->keys;

    /** @var BaseModel $model */
    $model = new $this->data['model'];
    $old_keys = $model->get_keys();
    $keys = [];

    foreach ($old_keys as $old_key) {
      $keys[$old_key] = $this->uniq_id . '_0_' . $old_key;
    }

    $this->keys = $keys;
    return $this->keys;
  }

  public function get_primary_key()
  {
    if (!is_null($this->primary_key)) return $this->primary_key;

    return ($this->primary_key = $this->data['model']::$primary_key);
  }

  /**
   * Adds all the eagerload from every related models
   */
  private function add_eager_load()
  {
    $with = $this->data['model']::$with;
    if (empty($with)) return;

    $model = new $this->data['model'];
    $model->initialize_relations();

    if (!is_a($model, BaseModel::class))
      throw new RelationDefinitionException("The model given is not a BaseModel child (" . $model::class . ")");

    foreach ($model->relation_info_maps as $v) {
      $v->parent_relation = $this;
    }

    $this->relations[$this->data['model']] = $model->relation_info_maps;
  }

  /**
   * @return DefaultQueryMaker
   */
  public function build_query()
  {
    if ($this->parent_model === null || !is_a($this->parent_model, BaseModel::class)) {
      throw new RelationDefinitionException("The parent model is null or is not a BaseModel object", $this);
    }

    // $query = SortedQueryMaker::select([$this->uniq_id . '_0_' => $this->parent_model::class], $this->uniq_id)->from($this->parent_model::$table, $this->uniq_id);
    $query = SortedQueryMaker::select([$this->uniq_id . '_0_' => $this->data['model']], $this->uniq_id)->from($this->data['model']::$table, $this->uniq_id);

    if (
      // Checks if the data['other_param'] exists
      isset($this->data['other_param'])
      // Checks that the data['other_param'] is a MysqlQueryable
      && is_a($c = $this->data['other_param'], MysqlQueryable::class)
    ) {
      /** @var MysqlQueryable $c */
      foreach ($c->elements as $e) {
        if ($e instanceof Where) {
          $e->add_prefix($this->uniq_id . '.');
        } elseif (is_array($e) && $e['type'] == Where::class) foreach ($e['elements'] as $w) {
          $w->add_prefix($this->uniq_id . '.');
        }
      }

      $query->push_query($c);
    }

    if (is_null($this->relations) || count($this->relations) <= 0) {
      return $query;
    }

    /**
     * @var SortedQueryMaker $query
     * @var Select $select_query
     */
    $select_query = $query->search_element(Select::class);

    // Gets all of the query per relations
    foreach ($this->relations as $model_name => $array_relations)
      foreach ($array_relations as $relation_name => $relation) {
        $temp_query = $relation->build_query();
        /** 
         * Adds the select of the previous query
         * @var Select $select 
         */
        $select = $temp_query->search_element(Select::class);

        if ($select === false)
          throw new RelationDefinitionException("The query of the relation doesn't have a Select element", $this);

        $select_query->add_select(array_values($select->old_select()));

        $uniq_id = $this->uniq_id;
        $my_id = $relation->data['my_id'];
        $other_id = $relation->data['other_id'];

        // Adds the query
        $query->join($temp_query, $relation->uniq_id, self::TYPES_TO_JOIN[$relation->type])->on(function () use ($uniq_id, $relation, $my_id, $other_id) {
          /** @var DefaultQueryMaker $this */
          $this->where("$uniq_id.$my_id", '=', $relation->uniq_id . "_0_$other_id", false);
        });
      }
    return $query;
  }
}
