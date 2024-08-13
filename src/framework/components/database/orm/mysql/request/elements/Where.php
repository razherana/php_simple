<?php

namespace framework\components\database\orm\mysql\request\elements;

use framework\components\database\orm\mysql\request\interfaces\MysqlElement;
use framework\components\database\orm\mysql\exceptions\MysqlWhereException;
use framework\components\database\orm\mysql\request\interfaces\MysqlGroupableElement;
use framework\components\database\orm\mysql\request\utils\MysqlEscaper;

class Where implements MysqlElement, MysqlGroupableElement
{
  private const TYPES = ["WHERE", "AND", "OR"];

  public const NONE = 0, AND = 1, OR = 2;

  public $data = [];

  public function __construct($element1, $condition, $element2, $type, $clean = true)
  {
    $this->data = [self::TYPES[$type], $element1, $condition, $element2];
    $this->clean($clean);
  }

  /**
   * To clean or not
   * @param bool $clean
   */
  private function clean($clean)
  {
    if ($this->data[3] == NULL)
      switch ($this->data[2]) {
        case '=':
          $this->data[2] = 'IS';
          break;
        case '!=':
          $this->data[2] = 'IS NOT';
      }

    if (!$clean) return;

    $this->data[3] = MysqlEscaper::clean_and_add_quotes($this->data[3]);
    $this->data[1] = MysqlEscaper::clean_only($this->data[1]);
  }

  public function decode(): string
  {
    return implode(' ', $this->data);
  }

  /**
   * Decode a where group
   * @param array $group
   */
  public static function decode_group($group): string
  {
    if (!isset($group['type']) || $group['type'] !== self::class) {
      throw new MysqlWhereException("This group is not a " . self::class . " group");
    }

    if ($group['type_where'] == self::NONE)
      throw new MysqlWhereException("A where group cannot be a NONE type");

    $term = self::TYPES[$group['type_where']];
    $decoded = [];

    if (count($group['elements']) <= 1)
      throw new MysqlWhereException("This where group contains only one or no elements, consider using normal and or where");

    // unset the "WHERE" of the first element of the group
    if (isset($group['elements'][array_key_first($group['elements'])]->data[0]))
      unset($group['elements'][array_key_first($group['elements'])]->data[0]);

    foreach ($group['elements'] as $e) {
      if (!($e instanceof self))
        throw new MysqlWhereException("This where group contains a none where elements (" . $e::class . ")");

      $decoded[] = $e->decode();
    }

    return "$term (" . implode(' ', $decoded) . ")";
  }
}
