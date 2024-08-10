<?php

namespace framework\components\database\orm\mysql\request\elements;

use framework\components\database\orm\mysql\request\interfaces\MysqlElement;
use framework\components\database\orm\mysql\request\utils\MysqlEscaper;

class InsertInto implements MysqlElement
{
  private $datas = [], $table_name = "";

  public function decode(): string
  {
    return 'INSERT INTO ' . $this->table_name . ' VALUES (' . implode(', ', $this->datas) . ')';
  }

  /**
   * @param string $table_name
   * @param string[] $values
   */
  public function __construct($table_name, $values)
  {
    $this->table_name = $table_name;
    $this->datas = $values;
    $this->decode_datas();
  }

  private function decode_datas()
  {
    $datas = $this->datas;

    foreach ($datas as $column => &$value) {
      $value = MysqlEscaper::clean_and_add_quotes($value);

      if (is_string($column)) {
        $value = implode(' ', [$column, $value]);
      }
    }

    $this->datas = array_values($datas);
  }
}
