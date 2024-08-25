<?php

namespace framework\components\database\orm\mysql\models\relations\exceptions;

use framework\base\exceptions\DefaultException;
use framework\components\database\orm\mysql\models\relations\maps\RelationInfoMap;

class RelationDefinitionException extends DefaultException {

  /**
   * @var RelationMapInfo $relation
   */
  public $relation;

  public function __construct($message, $relation = null)
  {
    $this->relation = $relation;
    parent::__construct($message);
  }
}
