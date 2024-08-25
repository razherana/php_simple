<?php

namespace framework\components\database\orm\mysql\models\exceptions;

use framework\base\exceptions\DefaultException;

class DefaultModelException extends DefaultException
{

  /**
   * @var BaseModel|mixed|null $model 
   */
  public $model = null;

  public function __construct($descr, $model = null)
  {
    $this->model = $model;
    parent::__construct($descr);
  }
}
