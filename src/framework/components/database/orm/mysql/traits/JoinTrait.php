<?php

namespace framework\components\database\orm\mysql\traits;

use framework\components\database\orm\mysql\request\elements\Join;
use framework\components\database\orm\mysql\request\MysqlQueryable;

trait JoinTrait
{
  use MysqlRequestTrait;

  /**
   * @param string|MysqlQueryable $table_or_query
   * @param ?string $as
   * @param int $type
   */
  public function join($table_or_query, $as = null, $type = Join::NONE)
  {
    $this->elements[] = new Join($table_or_query, $as, $type);
    return $this;
  }
}
