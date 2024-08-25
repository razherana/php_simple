<?php

namespace framework\components\database\orm\mysql\models;

use framework\components\database\orm\mysql\executers\conversions\ModelConversion;
use framework\components\database\orm\mysql\executers\MysqlQueryExecuter;
use framework\components\database\orm\mysql\models\exceptions\ModelDefinitionException;
use framework\components\database\orm\mysql\models\relations\traits\RelationTrait;
use framework\components\database\orm\mysql\queries\SortedQueryMaker;
use framework\components\database\orm\mysql\request\elements\From;
use framework\components\database\orm\mysql\traits\OrderTrait;
use framework\components\database\orm\mysql\traits\RawTrait;
use framework\components\database\orm\mysql\traits\SelectTrait;
use framework\components\database\orm\mysql\traits\WhereTrait;
use framework\components\database\orm\mysql\models\instances\DescribedColumn;
use framework\components\database\orm\mysql\request\elements\Select;
use ReflectionClass;

abstract class BaseModel extends SortedQueryMaker
{
  use WhereTrait, OrderTrait, SelectTrait, RelationTrait, RawTrait;

  /**
   * Gets all the traits inherited
   * @inheritdoc
   */
  protected static function use_traits(): array
  {
    // Traits from the class
    $traits = (new ReflectionClass(static::class))->getTraits();

    // Checks if the class is not already the BaseModel
    if (static::class != BaseModel::class) {
      $class = (new ReflectionClass(static::class))->getParentClass();

      while ($class !== false) {
        // We can add with + because the key is a trait name.
        $traits += $class->getTraits();

        if ($class != BaseModel::class) break;

        $class = $class->getParentClass();
      }
    }

    return $traits;
  }

  /**
   * Contains the table name
   * @var string $table
   */
  public static $table = '';

  /**
   * Contains the primary key column's name
   * @var string $primary_key
   */
  public static $primary_key = 'id';

  /**
   * Contains information about the table
   * @var DescribedColumn[] $described_columns
   */
  public $described_columns;

  /**
   * @param bool $initialize_from Set to <b>TRUE</b> if you want to initialize the FROM
   * @param bool $initialize_with Set to <b>TRUE</b> if you want to initialize WITH's
   */
  public function __construct($initialize_from = true, $initialize_with = true, $initialize_described_table = true)
  {
    // TODO: Check if table exists

    // Initialize FROM
    if ($initialize_from)
      $this->elements[] = new From(static::$table);

    // Initilialize with's
    if ($initialize_with) {
      $this->with_instance(static::$with);
    }

    if ($initialize_described_table) {
      $this->described_columns = ModelConversion::to_multiple_model_query_type("DESCRIBE " . static::$table, DescribedColumn::class, $this);
      $this->check_primary_key();
    }
  }

  /**
   * Get the column's name of the model
   * @return array<int, string>
   */
  public function get_keys($key = "Field")
  {
    $cols = $this->described_columns;
    $keys = [];

    foreach ($cols as $col) {
      $keys[] = $col->{$key};
    }

    return $keys;
  }

  /**
   * Checks if the primary key set exist
   * @throws ModelDefinitionException
   */
  protected function check_primary_key()
  {
    foreach ($this->described_columns as $column) if ($column->Field == $this::$primary_key)
      return;
    throw new ModelDefinitionException("The primary key given is not a column on this table : (" . $this::$primary_key . ")", $this);
  }

  public static function all($selects = ['*'])
  {
    return static::select($selects)->get();
  }

  public function decode_query(): string
  {
    if (!is_null($this->temp_query)) return $this->temp_query;

    $this->initialize_relations();

    if ($this->has_relations()) {
      $this->build_relation_query();
    }

    $query = $this;

    if ($query->search_element(Select::class) === false) {
      $query->select_instance(static::class);
    }

    return $this->temp_query = parent::decode_query();
  }

  public function get()
  {
    $q = $this->decode_query();

    // Gets all the data in query
    $all = MysqlQueryExecuter::do_clean($q, function ($e) {
      return $e->fetch_all(MYSQLI_ASSOC);
    });

    // If the query has relations, then use ModelConversion
    if ($this->has_relations()) {
      return ModelConversion::to_array_models_relation($all, $this);
    }

    // Else
    return ModelConversion::to_multiple_model($all, $this::class);
  }
}
