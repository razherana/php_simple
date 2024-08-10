<?php

namespace framework\components\route\closure;

use ReflectionFunction;
use SplFileObject;

class ClosureWrapper
{
  /**
   * Contains the closure in string
   * @var string $closure
   */
  private $closure;

  /**
   * @param \Closure $closure
   */
  private function __construct($closure)
  {
    $this->make_closure_string($closure);
  }

  public function get_closure()
  {
    return eval($this->closure);
  }

  /**
   * Makes the closure into a string
   * @param \Closure $closure
   */
  private function make_closure_string($closure)
  {
    $refl = new ReflectionFunction($closure);
    $start = $refl->getStartLine() - 1;
    $end = $refl->getEndLine();
    $file = new SplFileObject($refl->getFileName());
    $file->seek($start);

    $content = "";

    for ($i = 0; $i < $end - $refl->getStartLine() + 1; $i++) {
      $content .= $file->current();
      $file->next();
    }

    $content = substr($content, stripos($content, 'function'));

    for ($i = 0, $accolade_counter = -1; $i < strlen($content); $i++) {
      if ($accolade_counter == 0) {
        break;
      }
      if ($content[$i] == '{') {
        if ($accolade_counter != -1) $accolade_counter++;
        else $accolade_counter = 1;
      }
      if ($content[$i] == '}') $accolade_counter--;
    }
    $content = substr($content, 0, $i);
    $uses = [];
    $file->rewind();
    $pos = 0;
    while (($line = $file->current()) && $pos <= $start) {

      if ($line == '') {
        $pos++;
        $file->next();
        continue;
      }

      $line = trim($line);

      if ($line == '') {
        $pos++;
        $file->next();
        continue;
      }

      $offset = 0;

      while (($p = stripos($line, 'use ', $offset)) !== false) {
        for ($i = $p - 1, $entered = []; $i >= 0; $i--) {
          if ($line[$i] == ';') break;
          else
            $entered[] = $line[$i];
        }

        $entered = array_filter($entered, function ($el) {
          return !in_array($el, str_split(" \n\r\t\v\0"));
        });

        if (count($entered) > 0) {
          $offset = $p + 4;
          continue;
        }

        $end_pos = stripos($line, ';', $p);
        $uses[] = substr($line, $p, $end_pos - $p + 1);
        $offset = $end_pos + 1;
      }

      $pos++;
      $file->next();
    }

    $use = implode("\n", $uses);
    $content = $use . "\nreturn " . $content . ";";
    $this->closure = $content;
  }

  /**
   * Make a closure wrapper from a
   * closure
   * @param \Closure $closure
   */
  public static function from($closure): self
  {
    return new self($closure);
  }
}
