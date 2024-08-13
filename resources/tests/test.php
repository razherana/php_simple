<?php

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

class QueryMaker extends MysqlQueryable
{
  use SelectTrait, WhereTrait, FromTrait, OrderTrait, RawTrait, DeleteTrait, InsertIntoTrait, OnTrait, JoinTrait;

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

dd(QueryMaker::select()->from('users', 'u')->join("messages", "m")->on(fn () => $this->where('u.id', '=', 'm.id_sender', false)->and_where('u.id', '!=', null)->and_group_where(fn () => $this->where('u.id', '!=', 1)->and_where('m.id', '!=', 2)))->decode_query());
