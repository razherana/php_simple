<?php

use framework\components\database\orm\mysql\queries\DefaultQueryMaker;

dd(DefaultQueryMaker::select()->from('users', 'u')->join("messages", "m")->on(fn () => $this->where('u.id', '=', 'm.id_sender', false)->and_where('u.id', '!=', null)->and_group_where(fn () => $this->where('u.id', '!=', 1)->and_where('m.id', '!=', 2)))->decode_query());
