<?php

namespace framework\components\database\orm\mysql\traits;

use framework\components\database\orm\mysql\request\elements\Where;

/**
 * Represents a trait which contains 'where' functions
 * Only works in a MysqlQueryable element
 */
trait WhereTrait
{
  use MysqlRequestTrait;

  /**
   * Defines a new static with a where
   * @param string $condition
   */
  protected static function where_static($element1, $condition, $element2, $clean = true)
  {
    $el = new static();
    return $el->where_instance($element1, $condition, $element2, $clean);
  }

  protected function where_instance($element1, $condition, $element2, $clean = true)
  {
    $this->elements[] = new Where($element1, $condition, $element2, Where::NONE, $clean);
    return $this;
  }

  public function and_where($element1, $condition, $element2, $clean = true)
  {
    $this->elements[] = new Where($element1, $condition, $element2, Where::AND, $clean);
    return $this;
  }

  public function or_where($element1, $condition, $element2, $clean = true)
  {
    $this->elements[] = new Where($element1, $condition, $element2, Where::OR, $clean);
    return $this;
  }

  /**
   * Why public ?
   * Because you can only call this in an instance not static
   * @param \Closure $conditions
   */
  public function or_group_where($conditions)
  {
    $dummy = new static;
    $dummy->mode_test = true;
    $conditions = $conditions->bindTo($dummy, static::class);
    $conditions();

    $elements = $dummy->elements;
    $this->elements[] = ['type' => Where::class, Where::OR, $elements];

    return $this;
  }

  /**
   * Why public ?
   * Because you can only call this in an instance not static
   * @param \Closure $conditions
   */
  public function and_group_where($conditions)
  {
    $dummy = new static;
    $dummy->mode_test = true;
    $conditions = $conditions->bindTo($dummy, static::class);
    $conditions();

    $elements = $dummy->elements;
    $this->elements[] = [
      'type' => Where::class,
      'type_where' => Where::AND,
      'elements' => $elements
    ];

    return $this;
  }
}
