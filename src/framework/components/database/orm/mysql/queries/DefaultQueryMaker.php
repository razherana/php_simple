<?php

namespace framework\components\database\orm\mysql\queries;

use framework\components\database\orm\mysql\request\MysqlQueryable;
use framework\components\database\orm\mysql\traits\DeleteTrait;
use framework\components\database\orm\mysql\traits\FromTrait;
use framework\components\database\orm\mysql\traits\InsertIntoTrait;
use framework\components\database\orm\mysql\traits\JoinTrait;
use framework\components\database\orm\mysql\traits\OnTrait;
use framework\components\database\orm\mysql\traits\OrderTrait;
use framework\components\database\orm\mysql\traits\RawTrait;
use framework\components\database\orm\mysql\traits\SelectTrait;
use framework\components\database\orm\mysql\traits\WhereTrait;

/**
 * This is a default query maker who just decodes everything in order without re-arranging
 */
class DefaultQueryMaker extends MysqlQueryable
{
  use SelectTrait, WhereTrait, FromTrait, OrderTrait, RawTrait, DeleteTrait, InsertIntoTrait, OnTrait, JoinTrait;

  public function decode_query(): string
  {
    $this->verify_query();
    $els = [];
    foreach ($this->elements as $e) {
      if (is_array($e))
        $els[] = self::decode_array_query($e);
      else
        $els[] = $e->decode();
    }
    return implode(' ', $els);
  }
}
