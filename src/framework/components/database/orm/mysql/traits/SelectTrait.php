<?php

namespace framework\components\database\orm\mysql\traits;

use framework\components\database\orm\mysql\exceptions\MysqlSelectException;
use framework\components\database\orm\mysql\request\elements\Select;

/**
 * Represents a trait which contains 'where' functions
 * Only works in a MysqlQueryable element
 */
trait SelectTrait
{
  use MysqlRequestTrait;

  /**
   * @param array<int|string, string>|string $elements
   */
  protected static function select_static($elements = ['*'], $use_model_name_prefix = true)
  {
    $el = new static();
    return $el->select_instance($elements, $use_model_name_prefix);
  }

  /**
   * @param array<int|string, string>|string $elements
   */
  protected function select_instance($elements = ['*'], $use_model_name_prefix = true)
  {
    $this->elements[] = new Select($elements, $use_model_name_prefix);
    return $this;
  }

  /**
   * @param array<int|string, string>|string $elements
   */
  public function add_select($elements = [], $use_model_name_prefix = true)
  {
    /**
     * @var false|Select $select
     */
    $select = $this->search_element(Select::class);

    if ($select === false)
      throw new MysqlSelectException("Cannot add_select because there is no Select mysql element");

    $select->add_select($elements, $use_model_name_prefix);

    return $this;
  }
}
