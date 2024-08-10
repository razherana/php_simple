<?php

namespace framework\components\database\orm\mysql\traits;

use framework\components\database\orm\mysql\request\elements\Raw;

trait RawTrait
{
  use MysqlRequestTrait;

  /**
   * @param string $content
   */
  protected function raw_instance($content)
  {
    $this->elements[] = new Raw($content);
    return $this;
  }

  /**
   * @param string $content
   */
  protected static function raw_static($content)
  {
    $a = new static();
    return $a->raw_instance($content);
  }
}
