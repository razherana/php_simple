<?php

namespace vendor\main\uri;

use vendor\main\cache\sessions\Session;
use vendor\main\Facades;
use vendor\main\routing\RouteVars;

class Url extends Facades
{
  public static $functions = [
    "isCurrentUri" => "sameUri",
    "requestMethod" => "getRequestMethod",
    "getUri" => "getUriFacade",
    "getQuery" => "getQueryFacade"
  ];
  private $full_uri, $uri, $query;

  public static function removeQuery($full_uri)
  {
    if (\stripos($full_uri, '?') !== false) {
      return explode('?', $full_uri)[0];
    }
    return $full_uri;
  }

  public static function getQuery($full_uri)
  {
    if (\stripos($full_uri, '?') !== false) {
      return explode('?', $full_uri)[1];
    }
    return null;
  }

  public function __construct()
  {
    $this->full_uri = Session::get()->___uri___;
    $this->uri = self::removeQuery($this->full_uri);
    $this->query = self::getQuery($this->full_uri);
  }

  public function getQueryFacade()
  {
    return $this->query;
  }

  public function getUriFacade()
  {
    return $this->uri;
  }

  private function checkUriVars($uri)
  {
    $routeVars = new RouteVars($uri);
    return $routeVars->isSame($this->uri);
  }

  public function sameUri($arg)
  {
    $uri = $arg[0];
    if ($uri == $this->uri) return true;
    if (\stripos($uri, "<<") !== false && \stripos($uri, ">>") !== false) {
      return $this->checkUriVars($uri);
    }
  }

  public function getRequestMethod()
  {
    return ($_SERVER['REQUEST_METHOD'] ?? '');
  }

  public static function getLink(string $uri, array $query = [])
  {
    if (!empty($query))
      $q = \http_build_query($query);
    $full = getFolder() . $uri;
    if (!empty($query))
      $full .= '?' . $q;
    return $full;
  }

  public static function redirect(string $uri, array $query = [])
  {
    $uri = self::getLink($uri, $query);
    header("Location: $uri");
    exit;
  }
}
