<?php

use framework\base\config\ConfigReader;

$element = ConfigReader::get('functions', 'all_import');

foreach ($element as $fun_name) {
  require_once($fun_name . '.php');
}
