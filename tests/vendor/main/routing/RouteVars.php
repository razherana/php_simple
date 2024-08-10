<?php

namespace vendor\main\routing;

class RouteVars
{
  public $vars = [];
  public $uri = '';

  /**
   * From RouteInfo
   */
  public function __construct($uri)
  {
    $this->uri = $uri;
    $this->getVars();
  }

  private function getVars()
  {
    $els = explode('/', $this->uri);
    $els = array_merge(array_filter($els, function ($el) {
      return $el !== '';
    }));

    for ($i = 0; $i < count($els); $i++) {
      $el = $els[$i];
      if (
        strlen($el) >= 5
        && substr($el, 0, 2) == '<<'
        && substr($el, strlen($el) - 2) == '>>'
      ) {
        $el = str_replace('<<', '', $el);
        $el = str_replace('>>', '', $el);
        $this->vars[$i] = $el;
      }
    }
  }

  public function getVarsOfUri($an_uri)
  {
    $vars = $this->vars;
    $uri = array_merge(array_filter(explode('/', $an_uri), function ($el) {
      return $el !== '';
    }));
    $value_vars = [];

    foreach (array_keys($vars) as $var)
      $value_vars[] = $uri[$var];

    return $value_vars;
  }

  public function isSame($an_uri)
  {
    $els = explode('/', $this->uri);
    $uri_divised = explode('/', $an_uri);
    $els = array_merge(array_filter($els, function ($el) {
      return $el !== '';
    }));
    $uri_divised = array_merge(array_filter($uri_divised, function ($el) {
      return $el !== '';
    }));

    if (count($uri_divised) !== count($els)) return false;

    for ($i = 0; $i < count($els); $i++) {
      $el = $els[$i];
      if (
        strpos($el, '<<') === false &&
        strpos($el, '>>') === false &&
        $el != $uri_divised[$i]
      )
        return false;
    }
    return true;
  }
}
