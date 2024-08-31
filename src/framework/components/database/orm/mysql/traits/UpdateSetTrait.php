<?php

namespace framework\components\database\orm\mysql\traits;

use framework\components\database\orm\mysql\request\elements\UpdateSet;

trait UpdateSetTrait
{
  use MysqlRequestTrait;

  public static function update_set($table_name, $set_values)
  {
    $a = new static;
    $a->elements[] = new UpdateSet($table_name, $set_values);
    return $a;
  }
}
