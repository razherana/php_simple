<?php

namespace framework\components\database\orm\mysql\exceptions;

use framework\components\database\orm\mysql\request\elements\Select;

class MysqlSelectException extends QueryException
{
  /**
   * @var ?Select $select
   */
  public $select;

  public function __construct($message, $select = null) {
    parent::__construct($message);
    $this->select = $select;
  }
}
