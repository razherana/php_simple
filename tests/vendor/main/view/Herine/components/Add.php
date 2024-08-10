<?php

namespace vendor\main\view\Herine\components;

class Add
{
  private const TAG_START = ['<<add:', '<?php $___thisView___->add('], TAG_END = ['>>', ') ?>'];

  public $vars = [];
  public $viewName = [];
  public $blocks = [];

  public function __construct($viewName, $blocks, $vars = [])
  {
    $this->vars = $vars;
    $this->viewName = $viewName;
    $this->blocks = $blocks;
  }

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


      if (str_contains($interior, '::')) {
        $division = explode('::', $interior);
        $name = '"' . trim(rtrim($division[0])) . '"';
        $values = trim(rtrim($division[1]));
        $interior = $name . ',' . $values;
      } else {
        $interior = '"' . $interior . '"';
      }

      $content_part2 = self::TAG_END[1] . substr($content, $end_tag_position + strlen(self::TAG_END[0]));

      $content_part1 .= $interior;
      $content = $content_part1 . $content_part2;

      $depart = $end_tag_position;

      if ($depart > strlen($content)) break;

      $pos_reference = strpos($content, self::TAG_START[0], $depart);
    }
    return $content;
  }
}
