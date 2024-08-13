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
  public $from = '';

  /**
   * If the subquery is a MysqlQueryable
   */
  protected $is_query = false;

  /**
   * Contains the alias
   */
  public $as = null;

  /**
   * @param string|MysqlQueryable $from
   * @param string|null $as
   */
  public function __construct($from, $as = null)
  {
    if ($from instanceof MysqlQueryable) {
      $from = $from->decode_query();
      $this->is_query = true;
      if (!is_string($as)) throw new QueryException("AS is required when using a sub-query for FROM \n`" . $from . "`");
    }
    $this->from = $from;
    $this->as = $as;
  }

  public function decode(): string
  {
    $as = "";
    if (!is_null($this->as)) $as = ' AS ' . $this->as;

    $from = $this->from;
    // If FROM is a subquery, add parentheses
    if ($this->is_query) $from = "($from)";

    return "FROM " . $from . $as;
  }
}
