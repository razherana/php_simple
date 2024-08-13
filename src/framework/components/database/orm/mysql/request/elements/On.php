<?php

namespace framework\components\database\orm\mysql\request\elements;

use framework\components\database\orm\mysql\exceptions\MysqlJoinException;
use framework\components\database\orm\mysql\request\interfaces\MysqlElement;
use framework\components\database\orm\mysql\request\MysqlQueryable;

/**
 * ON condition of JOIN
 */
class On implements MysqlElement
{
  /**
   * Contains the where condition
   * @var Where[]|array $wheres
   */
  private $wheres;

  /**
   * @param \Closure $condition_callable
   * @param string $mysql_queryable_class
   */
  public function __construct($condition_callable, $mysql_queryable_class)
  {
    if (empty($mysql_queryable_class) || get_parent_class($mysql_queryable_class) !== MysqlQueryable::class) {
      throw new MysqlJoinException("The mysql_queryable_class : '$mysql_queryable_class' given is not a MysqlQueryable object");
    }

    // Creates an object of a mysql_queryable
    $mysql_queryable = new $mysql_queryable_class();
    $mysql_queryable->mode_test = true;

    // Bind to object and call it
    $condition_callable->bindTo($mysql_queryable, $mysql_queryable_class)();

    // Take the first element of the new object (where) and set it to property
    $this->wheres = $mysql_queryable->elements;

    // Removes first 6 characters "WHERE "
    if (count($this->wheres) > 0)
      unset($this->wheres[0]->data[0]);
  }

  public function decode(): string
  {
    $decoded = [];

    foreach ($this->wheres as $where) {
      if (is_array($where))
        $decoded[] = Where::decode_group($where);
      else
        $decoded[] = $where->decode();
    }

    return "ON (" . implode(" ", $decoded) . ")";
  }
}
