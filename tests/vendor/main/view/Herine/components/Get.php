<?php

namespace vendor\main\view\Herine\components;

class Get
{
  public const TAG_START = ["<<get:", '<?php $___thisView___->get('], TAG_END = ['>>', ') ?>'];

  public static function compile($content)
  {
    $depart = 0;
    $pos_reference = strpos($content, self::TAG_START[0]);

    while ($pos_reference !== false) {
      $open_tag_position = $pos_reference;
      $end_tag_position = strpos($content, self::TAG_END[0], $open_tag_position);

      $content_part1 = substr($content, 0, $open_tag_position - 1) . self::TAG_START[1];

      $interior = substr($content, $open_tag_position + strlen(self::TAG_START[0]), $end_tag_position - ($open_tag_position + strlen(self::TAG_START[0])));
      $interior = trim($interior);
      $interior = rtrim($interior);
      $interior = '"' . $interior . '"';

      $content_part2 = self::TAG_END[1] . substr($content, $end_tag_position + strlen(self::TAG_END[0]));

      $content = $content_part1 . $interior . $content_part2;
      $depart = strlen($content_part1) + strlen($interior) + strlen(self::TAG_END[1]);

      if ($depart > strlen($content)) break;

      $pos_reference = strpos($content, self::TAG_START[0], $depart);
    }

    return $content;
  }
}
