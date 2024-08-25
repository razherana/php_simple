<?php

namespace framework\components\database\orm\mysql\models\relations\traits;

use BadMethodCallException;
use framework\components\database\orm\mysql\models\relations\exceptions\RelationDefinitionException;
use framework\components\database\orm\mysql\traits\MysqlRequestTrait;
use framework\components\database\orm\mysql\models\relations\maps\RelationInfoMap;
use framework\components\database\orm\mysql\queries\DefaultQueryMaker;
use framework\components\database\orm\mysql\models\BaseModel;
use framework\components\database\orm\mysql\request\elements\Select;

trait RelationTrait
{
  use MysqlRequestTrait;

  /**
   * Contains all of the eager load relations
   * @var string[] $with
   */
  public static $with = [];

  /**
   * Contains all of the eager load relations
   * for this instance
   * @var string[] $with_instance
   */
  protected $with_instance = [];

  /**
   * Contains all of the relations of the element
   * @var array<string, RelationInfoMap> $relation_info_maps
   */
  public $relation_info_maps = [];

  /**
   * Add new elements to eager load
   * @param string[] $elements
   */
  protected static function with_static($elements = [])
  {
    $a = new static();
    return $a->with_instance($elements);
  }

  /**
   * Add new elements to eager load
   * @param string[] $elements
   */
  protected function with_instance($elements = [])
  {
    $this->with_instance = array_merge($this->with_instance, $elements);
    return $this;
  }

  /**
   * Adds an eager-load relation
   * @param string $relation_name 
   * @param \Closure $callable This closure is binded with the Model
   */
  protected static function relation_static($relation_name, $callable)
  {
    $a = new static();
    return $a->relation_instance($relation_name, $callable);
  }

  /**
   * Adds an eager-load relation
   * @param string $relation_name 
   * @param \Closure $callable This closure is binded with the Model
   */
  protected function relation_instance($relation_name, $callable)
  {
    $relation = $callable->call($this);

    if (!($relation instanceof RelationInfoMap)) {
      throw new RelationDefinitionException('This result of the callable is not a RelationMapInfo (' . print_r($callable, true) . ')', null);
    }

    $this->relation_info_maps[$relation_name] = $relation;
    return $this;
  }

  /**
   * Defines a new has_many relation
   * 
   * @param string $model Contains the Model::class to add a relation to
   * @param \Closure|null $other_param This closure applies to a new 
   * SortedQueryMaker for more choice on the query
   */
  protected function has_many($model, $my_id, $other_id, $other_param = null)
  {
    // Creates a new empty default query maker
    $query_maker = new DefaultQueryMaker;

    // Calls the closure if it is not null
    if (is_callable($other_param)) {
      $other_param->call($query_maker);
    }

    $data =
      // Adds obligatory elements
      compact('model', 'my_id', 'other_id')
      +
      // Adds the query_maker to the $data if not null and callable
      (!empty($other_param) && is_callable($other_param) ? [
        'other_param' => $query_maker
      ] : []);

    return new RelationInfoMap($this, RelationInfoMap::HAS_MANY, $data, true);
  }

  /**
   * Defines a new belongs_to relation
   * 
   * @param string $model Contains the Model::class to add a relation to
   * @param \Closure|null $other_param This closure applies to a new 
   * SortedQueryMaker for more choice on the query
   */
  protected function belongs_to($model, $my_id, $other_id, $other_param = null)
  {
    // Creates a new empty default query maker
    $query_maker = new DefaultQueryMaker;

    // Calls the closure if it is not null
    if (is_callable($other_param)) {
      $other_param->call($query_maker);
    }

    $data =
      // Adds obligatory elements
      compact('model', 'my_id', 'other_id')
      +
      // Adds the query_maker to the $data if not null and callable
      (!empty($other_param) && is_callable($other_param) ? [
        'other_param' => $query_maker
      ] : []);

    return new RelationInfoMap($this, RelationInfoMap::BELONGS_TO, $data, true);
  }

  /**
   * Defines a new one_to_one relation
   * 
   * @param string $model Contains the Model::class to add a relation to
   * @param \Closure|null $other_param This closure applies to a new 
   * SortedQueryMaker for more choice on the query
   */
  protected function one_to_one($model, $my_id, $other_id, $other_param = null)
  {
    // Creates a new empty default query maker
    $query_maker = new DefaultQueryMaker;

    // Calls the closure if it is not null
    if (is_callable($other_param)) {
      $other_param->call($query_maker);
    }

    $data =
      // Adds obligatory elements
      compact('model', 'my_id', 'other_id')
      +
      // Adds the query_maker to the $data if not null and callable
      (!empty($other_param) && is_callable($other_param) ? [
        'other_param' => $query_maker
      ] : []);

    return new RelationInfoMap($this, RelationInfoMap::ONE_TO_ONE, $data, true);
  }

  /**
   * Loads all the relations into the models
   */
  public function initialize_relations(): void
  {
    /**
     * @var string $with
     */
    foreach ($this->with_instance as $with) {
      if (method_exists(static::class, $with)) {
        $relation = $this->{$with}();

        if (!($relation instanceof RelationInfoMap)) {
          throw new RelationDefinitionException('This method in $with is not a relation, content : ' . print_r($relation, true), $relation);
        }

        $this->relation_info_maps[$with] = $relation;
      } else {
        throw new BadMethodCallException("This method does not exist ($with) in \$this::\$with => [ " . implode(', ', $this::$with) . " ]", 1);
      }
    }
  }

  protected function has_relations()
  {
    return count($this->relation_info_maps) > 0;
  }

  protected function build_relation_query()
  {
    /** @var DefaultQueryMaker $query */
    $query = $this;

    /** @var Select $select_query */
    $select_query = $query->search_element(Select::class);
    if ($select_query === false) {
      $query->select($query::class);
      $select_query = $query->search_element(Select::class);
    }

    if (!empty($this->relation_info_maps)) foreach ($this->relation_info_maps as $rel_name => $relation) {
      $temp_query = $relation->build_query();
      /** 
       * Adds the select of the previous query
       * @var Select $select 
       */
      $select = $temp_query->search_element(Select::class);

      if ($select === false)
        throw new RelationDefinitionException("The query of the relation doesn't have a Select element", $this);

      /** @var Select $select_query */
      $select_query->add_select(array_values($select->old_select()));

      /** @var BaseModel $this */
      $table = $this::$table;
      $my_id = $relation->data['my_id'];
      $other_id = $relation->data['other_id'];

      // Adds the query
      $query->join($temp_query, $relation->uniq_id, RelationInfoMap::TYPES_TO_JOIN[$relation->type])->on(function () use ($table, $relation, $my_id, $other_id) {
        /** @var DefaultQueryMaker $this */
        $this->where("$table.$my_id", '=', $relation->uniq_id . "_0_$other_id", false);
      });
    }

    return $query;
  }
}
