<?php

namespace vendor\main\routing;

use ReflectionFunction;
use SplFileObject;

class RouteClosure
{
  private $closure;

  public static function from($closure)
  {
    return new self($closure);
  }

  public function invoke()
  {
    return eval('return (' . $this->closure . ') ;');
  }

  private function __construct($closure)
  {
    $this->closure = self::getCode($closure);
  }

  private static function getCode($closure)
  {
    $reflector = new ReflectionFunction($closure);
    $file = new SplFileObject($reflector->getFileName());
    $str_start = 'RouteClosure::from(';
    $file->seek($reflector->getStartLine() - 1,);
    $pos = strpos($file->current(), $str_start) + strlen($str_start);
    $code = '';
    while ($file->key() < $reflector->getEndLine()) {
      $code .= $file->current();
      $file->next();
    }
    $code = substr($code, $pos);
    $start = strpos($code, 'function');
    if ($start === false) {
      $start = strpos($code, 'fn');
      $nbpar = 1;
      $temp_code = substr($code, strpos($code, '()', $start + 2));
      $end = 0;
      foreach (str_split($temp_code) as $k => $l) {
        if ($nbpar == 0) {
          $end = $k + 1;
          break;
        }
        if ($l == '(') $nbpar++;
        if ($l == ')') $nbpar--;
      }
    } else {
      $end = strrpos($code, "}");
    }
    $code = substr($code, $start, $end - $start + 1);
    return $code;
  }
}
