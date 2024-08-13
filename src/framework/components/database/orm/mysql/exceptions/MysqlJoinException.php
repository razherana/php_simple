<?php

namespace framework\components\database\orm\mysql\exceptions;

/**
 * mysql Join exception
 */
class MysqlJoinException extends QueryException
{
  public $join = null;

  public function __construct($description, $join = null)
  {
    parent::__construct($description);
    $this->join = $join;
  }
}
