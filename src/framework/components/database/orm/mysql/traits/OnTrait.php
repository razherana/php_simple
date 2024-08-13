<?php

namespace framework\components\database\orm\mysql\traits;

use framework\components\database\orm\mysql\request\elements\On;

trait OnTrait
{
  use MysqlRequestTrait;

  /**
   * @param \Closure $condition_callable
   */
  public function on($condition_callable)
  {
    $this->elements[] = new On($condition_callable, static::class);
    return $this;
  }
}
