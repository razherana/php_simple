<?php

namespace framework\components\database\orm\mysql\request\elements;

use framework\components\database\orm\mysql\request\interfaces\MysqlElement;

class Order implements MysqlElement
{
  private const TYPE = ["DESC", "ASC"];

  public const DESC = 0, ASC = 1;

  private $data = [];

  public function decode(): string
  {
    return "ORDER BY " . implode(' ', $this->data);
  }

  public function __construct($type, $column)
  {
    $this->data = [$column, self::TYPE[$type]];
  }
}
