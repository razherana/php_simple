<?php

use vendor\main\cache\sessions\Session;
use vendor\main\request\Request;
use vendor\main\routing\singleton\RouteSave;
use vendor\main\util\Message;
use vendor\main\view\View;

function dd($v = null)
{
  var_dump($v);
  die;
}

function getSessId()
{
  return Session::getSessId();
}

function e($string)
{
  if ($string === null) return;
  return htmlspecialchars($string);
}

function getFolder()
{
  return substr($_SERVER['SCRIPT_NAME'], 0, strpos($_SERVER['SCRIPT_NAME'], (include(___DIR___ . "/config/app.php"))["public_folder"] ?? '/public'));
}

function dataViewVars()
{
  return Session::get()->___viewVar___->getAll();
}

function dataView()
{
  return Session::get()->___viewVar___;
}

function view($file, $array = [])
{
  View::from($file, $array);
}

function view_herine($file, $array = [])
{
  View::herine($file, $array);
}

function asset($s)
{
  return getFolder() . "/assets/" . $s;
}

function dump($v = null)
{
  var_dump($v);
}

function request()
{
  return new Request;
}

function makeUri($uri)
{
  return getFolder() . $uri;
}

function route($name, $args = [])
{
  if (RouteSave::$routeSave === null)
    throw new \Exception("Route Inexistante", 1);
  foreach (RouteSave::$routeSave as $route)
    if ($route->getName() === $name) {
      $uri = $route->getUri();

      if (!empty($route->routeVars->vars)) {

        if (count($route->routeVars->vars) > count($args)) {
          throw new \Exception("Missing arguments in " . $route->getName(), 1);
        }

        $i = 0;
        $keys = array_keys($args);

        for (; $i < count($route->routeVars->vars); $i++) {
          $uri = str_replace("<<" . $keys[$i] . ">>", $args[$keys[$i]], $uri);
        }
      }
      $argss = [];
      for ($k = ($i ?? 0), $keys = array_keys($args); $k < count($args); $k++) {
        $argss[] = $keys[$k] . "=" . $args[$keys[$k]];
      }
      return makeUri($uri . (!empty($argss) ? "?" . implode("&", $argss) : ""));
    }
  throw new \Exception("Route Inexistante", 1);
}

function to_route($name, $args = [])
{
  header('Location: ' . route($name, $args));
  exit;
}

function message($name, $type = 'default')
{
  foreach (Session::get()->___msg___ as $msg)
    if ($msg->name == $name && ($type == false || $type == $msg->type)) {
      $a = $msg->message;
      $msg->delete();
      return $a;
    }
  return null;
}

function error($name)
{
  foreach (Message::all() as $msg)
    if ($msg->name == $name && 'error' == $msg->type) {
      $a = $msg->message;
      $msg->delete();
      return $a;
    }
  return null;
}

function hasError($name)
{
  foreach (Message::all() as $msg)
    if ($msg->name == $name && 'error' == $msg->type) {
      return true;
    }
  return false;
}

/**
 * Best to follow with exit()
 */
function abort_404()
{
  include(___DIR___ . '/vendor/main/error_pages/404.php');
}

/**
 * Best to follow with exit()
 */
function abort_419()
{
  include(___DIR___ . '/vendor/main/error_pages/419.php');
}
