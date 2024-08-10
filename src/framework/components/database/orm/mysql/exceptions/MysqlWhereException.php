<?php

namespace framework\components\database\orm\mysql\exceptions;

class MysqlWhereException extends QueryException
{
  /**
   * Constructs a Mysql where exception
   */
  public function __construct($description)
  {
    parent::__construct($description);
  }
}
