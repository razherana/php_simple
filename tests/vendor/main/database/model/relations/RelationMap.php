<?php

namespace vendor\main\database\model\relations;

class RelationMap
{
  public $model1, $model2;
  public $type;
  public $uuid1, $uuid2;
  public $relation;

  private function __construct($model1, $model2, $type, $relation)
  {
    $this->model1 = $model1;
    $this->model2 = $model2;
    $this->type = $type;
    $this->uuid1 = bin2hex(random_bytes(4));
    $this->uuid2 = bin2hex(random_bytes(4));
    $this->relation = $relation;
  }

  public static function make($model1, $model2, $type, $relation)
  {
    return new self($model1, $model2, $type, $relation);
  }

  public function __toString()
  {
    return $this->model1 . '|#|' . $this->model2 . '|#|' . $this->type . "|#|" . $this->uuid1 . "|#|" . $this->uuid2;
  }
}
