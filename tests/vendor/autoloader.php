<?php

function autoload($className)
{
  $string = ___DIR___ . '/' . str_replace("\\", "/", $className) . ".php";
  if (file_exists($string)) {
    require_once($string);
  }
}

spl_autoload_register('autoload');
