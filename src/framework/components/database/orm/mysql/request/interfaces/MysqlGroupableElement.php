<?php

namespace framework\components\database\orm\mysql\request\interfaces;

interface MysqlGroupableElement
{
  /**
   * Decode a where group
   * @param array $group
   */
  public static function decode_group($group): string;
}
