<?php

namespace vendor\main\request\validating;

use DateTime;

class Rule
{
  public $field = '';
  public const number = 0, text = 1, in = 2, superior = 3, inferior = 4, not = 5, required = 6, optional = 7, superiorOrEqual = 8, equal = 9, inferiorOrEqual = 10, requiredFile = 11;

  /**
   * Array of rules
   */
  public $rules = [];

  /**
   * Set to a value for the message to display
   */
  public $message = [];

  protected function __construct($field)
  {
    $this->field = $field;
  }

  public static function field($field)
  {
    return new Rule($field);
  }

  public function required()
  {
    $this->rules[] = ['type' => self::required];
    return $this;
  }

  public function requiredFile()
  {
    $this->rules[] = ['type' => self::requiredFile];
    return $this;
  }

  public function optional()
  {
    $this->rules[] = ['type' => self::optional];
    return $this;
  }

  public function number()
  {
    $this->rules[] = ['type' => self::number];
    return $this;
  }

  public function text()
  {
    $this->rules[] = ['type' => self::text];
    return $this;
  }

  public function in(...$elements)
  {
    $this->rules[] = ['type' => self::in, 'args' => $elements];
    return $this;
  }

  /**
   * Get the value inside of a field for rules
   */
  public static function valueof($field)
  {
    return ['valueof' => $field];
  }

  public function equal($value)
  {
    $this->rules[] = ['type' => self::equal, 'args' => $value];
    return $this;
  }

  public function superior($value)
  {
    $this->rules[] = ['type' => self::superior, 'args' => $value];
    return $this;
  }


  public function superiorOrEqual($value)
  {
    $this->rules[] = ['type' => self::superiorOrEqual, 'args' => $value];
    return $this;
  }

  public function inferior($value)
  {
    $this->rules[] = ['type' => self::inferior, 'args' => $value];
    return $this;
  }

  public function inferiorOrEqual($value)
  {
    $this->rules[] = ['type' => self::inferiorOrEqual, 'args' => $value];
    return $this;
  }

  public function not($rules)
  {
    $temp = new Rule('');
    $rules($temp);
    $this->rules[] = ['type' => self::not, 'args' => $temp->rules];
    return $this;
  }

  public function message($message)
  {
    $this->message = $message;
    return $this;
  }
}
