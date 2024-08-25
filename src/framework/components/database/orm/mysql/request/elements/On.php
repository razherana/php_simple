<?php

namespace framework\components\database\orm\mysql\request\elements;

use ErrorException;
use framework\components\database\orm\mysql\exceptions\MysqlJoinException;
use framework\components\database\orm\mysql\queries\DefaultQueryMaker;
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

    // Creates an object of a mysql_queryable
    $mysql_queryable = new DefaultQueryMaker();
    $mysql_queryable->mode_test = true;

    // Bind to object and call it
    $condition_callable->call($mysql_queryable);

    // Take the first element of the new object (where) and set it to property
    $wheres = $mysql_queryable->elements;

    // Removes first 6 characters "WHERE "
    if (count($wheres) > 0) {
      $data = $wheres[0]->data;
      unset($data[0]);

      $wheres[0]->data = $data;
    }

    $this->wheres = $wheres;
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
