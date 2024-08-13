<?php

namespace framework\components\database\orm\mysql\exceptions;

/**
 * mysql on exception
 */
class MysqlOnException extends QueryException
{
  public $on = null;

  public function __construct($description, $on = null)
  {
    parent::__construct($description);
    $this->on = $on;
  }
}
