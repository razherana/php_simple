<?php

namespace vendor\main\util;

class RandomGenerator
{
  public static function randomString($length = 4)
  {
    $length -= $length % 2;
    return bin2hex(random_bytes($length / 2 > 0 ? $length / 2 : 1));
  }
}
