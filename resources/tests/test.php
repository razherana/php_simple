<?php

use framework\components\database\orm\mysql\queries\SortedQueryMaker;

dd(SortedQueryMaker::where('test.id', '=', 6)->select()->and_where('users.id', '!=', null)->from('test')->join('users')->on(fn () => $this->where('users.id', '!=', null))->decode_query());
