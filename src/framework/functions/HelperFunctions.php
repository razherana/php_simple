<?php

use framework\components\route\storage\RouteSave;
use framework\http\Request;

function parenturi()
{
  return Request::$parent_folder ?? Request::get_from_global_vars()::$parent_folder;
}

function asset($url)
{
  return parenturi() . trim($url, ' /');
}

function route($route_name, $args = [])
{
  return parenturi() . trim(RouteSave::uri_from_route_name($route_name, $args), '/');
}
