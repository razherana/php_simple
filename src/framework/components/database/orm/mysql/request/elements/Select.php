<?php

namespace framework\components\database\orm\mysql\request\elements;

use framework\components\database\orm\mysql\request\interfaces\MysqlElement;

class Select implements MysqlElement
{
  /**
   * Contains the select elements
   */
  protected $select = [];

  /**
   * @param string[] $elements
   */
  public function __construct($elements = ['*'])
  {
    $this->select = $elements;
    $this->clean();
  }

  private function clean()
  {
    foreach ($this->select as $k => &$v)
      if (is_string($k)) {
        $v = "$k AS $v";
      }
    $this->select = array_values($this->select);
  }

  public function decode(): string
  {
    return 'SELECT ' . implode(',', $this->select);
  }
}
