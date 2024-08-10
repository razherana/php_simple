<?php

namespace framework\components\database\orm\mysql\traits;

use framework\components\database\orm\mysql\request\elements\Delete;

trait DeleteTrait
{
  use MysqlRequestTrait;

  public static function delete()
  {
    $a = new static;
    $a->elements[] = new Delete;
    return $a;
  }
}
