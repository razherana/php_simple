<?php

namespace framework\components\database\orm\mysql\request\elements;

use framework\components\database\orm\mysql\request\interfaces\MysqlElement;

class Delete implements MysqlElement
{
  public function decode(): string
  {
    return 'DELETE';
  }
}
