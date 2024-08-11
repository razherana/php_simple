<?php

use framework\components\database\orm\mysql\request\MysqlQueryable;
use framework\components\database\orm\mysql\traits\DeleteTrait;
use framework\components\database\orm\mysql\traits\FromTrait;
use framework\components\database\orm\mysql\traits\InsertIntoTrait;
use framework\components\database\orm\mysql\traits\OrderTrait;
use framework\components\database\orm\mysql\traits\RawTrait;
use framework\components\database\orm\mysql\traits\SelectTrait;
use framework\components\database\orm\mysql\traits\WhereTrait;

class QueryMaker extends MysqlQueryable
{
  use SelectTrait, WhereTrait, FromTrait, OrderTrait, RawTrait, DeleteTrait, InsertIntoTrait;

  public static function get_magic()
  {
  }

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

dd(QueryMaker::select()->from('test')->where('id', '!=', NULL)->and_group_where(fn () => $this->where('text', '=', 10)->and_where('id', '=', 1))->decode_query());
