<?php

namespace vendor\main\database\model;

class DescribedTable
{
  private $data, $fromModel;

  public function __construct($data, $fromModel)
  {
    $this->data = $data;
    $this->fromModel = $fromModel;
  }

  public function getAttributeName()
  {
    $el = [];
    foreach ($this->data as $v) {
      $el[] = $v['Field'];
    }
    return $el;
  }

  public function getAttributeType()
  {
    $el = [];
    foreach ($this->data as $v) {
      $el[] = $v['Type'];
    }
    return $el;
  }
}
