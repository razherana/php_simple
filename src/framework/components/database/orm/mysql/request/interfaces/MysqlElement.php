<?php

namespace framework\components\database\orm\mysql\request\interfaces;

interface MysqlElement
{
  /**
   * Decodes the current MysqlElement into a string
   * understandable by mysql
   * @return string
   */
  public function decode(): string;
}
