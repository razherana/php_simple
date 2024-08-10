<?php

use vendor\main\cache\sessions\Session;

// ini_set('session.cookie_secure', 1);
// ini_set('session.cookie_httponly', 1);
//ini_set('session.gc_maxlifetime', 1800);

session_start();
Session::setLastActivity();
