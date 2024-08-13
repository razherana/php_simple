<?php

namespace framework\components\database\orm\mysql\exceptions;

use framework\components\database\orm\mysql\request\MysqlQueryable;

/**
 * Exception throwing when decoding a query
 */
class MysqlQueryDecodingException extends QueryException
{
  /**
   * Contains the query
   * @var MysqlQueryable $query
   */
  public $query = null;

  public function __construct($description, $query = null) 
  {
    parent::__construct($description);
    $this->query = $query;
  }
}
