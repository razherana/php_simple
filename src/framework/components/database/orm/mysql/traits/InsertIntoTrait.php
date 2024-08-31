<?php

namespace framework\components\database\orm\mysql\traits;

use framework\components\database\orm\mysql\request\elements\InsertInto;

trait InsertIntoTrait
{
  use MysqlRequestTrait;

  /**
   * @param string $table_name
   * @param string[] $values
   */
  public static function insert_into($table_name, $values)
  {
    $a = new static();
    $a->elements[] = new InsertInto($table_name, $values);
    return $a;
  }
}
