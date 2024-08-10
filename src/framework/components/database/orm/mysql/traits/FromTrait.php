<?php

namespace framework\components\database\orm\mysql\traits;

use framework\components\database\orm\mysql\request\elements\From;
use framework\components\database\orm\mysql\request\MysqlQueryable;

trait FromTrait
{
  use MysqlRequestTrait;

  /**
   * @param string|MysqlQueryable $element
   */
  public function from($element, $as = null)
  {
    $this->elements[] = new From($element, $as);
    return $this;
  }
}
