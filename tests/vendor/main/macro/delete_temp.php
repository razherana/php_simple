<?php

use vendor\main\cache\sessions\Session;
use vendor\main\database\connection\Database;

Session::delete('temp_user');
Session::delete('___viewVar___');
Database::get()->close();
