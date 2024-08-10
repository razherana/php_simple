<?php

// set Message if doesnt exist
use vendor\main\cache\sessions\Session;

Session::save('___uri___', substr_replace(urldecode($_SERVER['REQUEST_URI']), '', strpos(urldecode($_SERVER['REQUEST_URI']), getFolder()), strlen(getFolder())));

if (is_null(Session::get()->___msg___))
  Session::set('___msg___', []);
