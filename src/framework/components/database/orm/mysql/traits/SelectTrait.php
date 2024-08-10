<?php

namespace framework\components\database\orm\mysql\traits;

use framework\components\database\orm\mysql\request\elements\Select;

/**
 * Represents a trait which contains 'where' functions
 * Only works in a MysqlQueryable element
 */
trait SelectTrait
{
  use MysqlRequestTrait;

  /**
   * @param string[] $elements
   */
  protected static function select_static($elements = ['*'])
  {
    $el = new static();
    return $el->select_instance($elements);
  }

  /**
   * @param string[] $elements
   */
  protected function select_instance($elements = ['*'])
  {
    $this->elements[] = new Select($elements);
    return $this;
  }
}
