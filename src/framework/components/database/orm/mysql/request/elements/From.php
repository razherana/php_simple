<?php

namespace framework\components\database\orm\mysql\request\elements;

use framework\components\database\orm\mysql\exceptions\QueryException;
use framework\components\database\orm\mysql\request\interfaces\MysqlElement;
use framework\components\database\orm\mysql\request\MysqlQueryable;

class From implements MysqlElement
{
  /**
   * Contains the from element
   * @var string $from
   */
  private $from = '';

  /**
   * Contains the alias
   */
  private $as = null;

  /**
   * @param string|MysqlQueryable $from
   * @param string|null $as
   */
  public function __construct($from, $as = null)
  {
    if ($from instanceof MysqlQueryable) {
      $from = $from->decode_query();
      if (!is_string($as)) throw new QueryException("AS is required when using a sub-query for FROM \n`" . $from . "`");
    }
    $this->from = $from;
    $this->as = $as;
  }

  public function decode(): string
  {
    return 'FROM (' . $this->from . ')' . (is_null($this->as) ? '' : (" AS " . $this->as));
  }
}
