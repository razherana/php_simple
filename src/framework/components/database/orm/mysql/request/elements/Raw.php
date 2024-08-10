<?php

namespace framework\components\database\orm\mysql\request\elements;
use framework\components\database\orm\mysql\request\interfaces\MysqlElement;

class Raw implements MysqlElement
{
  private $content = '';

  /**
   * @param string $content
   */
  public function __construct($content)
  {
    $this->content = $content;
  }

  public function decode(): string
  {
    return $this->content;
  }
}
