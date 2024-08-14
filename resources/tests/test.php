<?php

use framework\components\database\orm\mysql\queries\SortedQueryMaker;

dd(SortedQueryMaker::where('test.id', '=', 6)
  ->select()
  ->from('test')
  ->and_group_where(function () {
    $this->where('herana', 'LIKE', '%herana%')
      ->or_where('test', '>', 6)
      ->and_where('herana', '!=', 'herana');
  })
  ->and_where('users.id', '!=', null)
  ->join('users')
  ->on(fn () => $this->where('users.id', '!=', null))
  ->decode_query());
