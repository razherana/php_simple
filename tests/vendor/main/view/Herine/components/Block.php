<?php

namespace vendor\main\view\Herine\components;

class Block
{
  private const CLOSE_TAG = ["<<endblock>>", '<?php $___thisView___->endBlock() ?>'], OPEN_TAG = ['<<block:', '<?php $___thisView___->startBlock(\''], END_OPEN_TAG = ['>>', '\')?>'];

  public $name = "";
  public $content = "";

  public function __construct($name)
  {
    $this->name = $name;
  }

  private static function compileStart($content)
  {
    $depart = 0;
    $pos_reference = strpos($content, self::OPEN_TAG[0]);
    while ($pos_reference !== false) {
      $open_tag_position = $pos_reference;
      $end_tag_position = strpos($content, self::END_OPEN_TAG[0], $open_tag_position);

      $content_part1 = substr($content, 0, $open_tag_position - 1);
      $content_part2 = self::END_OPEN_TAG[1] . substr($content, $end_tag_position + strlen(self::END_OPEN_TAG[0]));

      $block_name = substr($content, $open_tag_position + strlen(self::OPEN_TAG[0]), $end_tag_position + strlen(self::END_OPEN_TAG[0]) - $open_tag_position - strlen(self::OPEN_TAG[0]) - 2);
      $block_name = rtrim($block_name);
      $block_name = trim($block_name);

      $content_part1 .= self::OPEN_TAG[1] . $block_name;
      $content = $content_part1 . $content_part2;
      $depart = strlen($content_part1) + strlen(self::END_OPEN_TAG[1]);
      if ($depart > strlen($content)) break;
      $pos_reference = strpos($content, self::OPEN_TAG[0], $depart);
    }
    return $content;
  }

  public static function compile($content)
  {
    $content = self::compileStart($content);
    $content = str_replace(self::CLOSE_TAG[0], self::CLOSE_TAG[1], $content);
    return $content;
  }
}
