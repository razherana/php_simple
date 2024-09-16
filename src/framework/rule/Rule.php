<?php

namespace framework\rule;

use framework\rule\validation\OneValidation;

class Rule
{
  public const REQUIRED = 'req',
    OPTIONAL = 'opt',
    NUMBER = 'num',
    SUPERIOR = 'sup',
    INFERIOR = 'inf',
    EQUALS = 'equ',
    INFERIOR_OR_EQUALS = 'infeq',
    SUPERIOR_OR_EQUALS = 'supeq',
    VALUEOF = 'val',
    NOT = 'not',
    IN = 'in',
    DATE_FORMAT = 'datform',
    DATE = 'date',
    DATETIME = 'datetime',
    UNIQUE = 'uniq',
    INNER_CHECKING = 'inner',
    OTHER = 'othe';

  public function __construct($content)
  {
    $this->content = $content;
  }

  public static function from($content)
  {
    return new static($content);
  }

  /**
   * Rules to be checked for
   * @var array $rules
   */
  public $rules = [];

  /**
   * The content to make use rules onto
   * @var mixed $content
   */
  public $content;

  /**
   * The content's name to use rules onto inside ArrayValidation
   * @var mixed $content_name
   */
  public $content_name = null;

  /**
   * Contains custom messages
   */
  public $messages = [];

  /**
   * Checks the content if exists
   */
  public function required()
  {
    if (!isset($this->rules[self::OPTIONAL]) || $this->rules[self::OPTIONAL] !== true)
      $this->rules[self::REQUIRED] = true;
    return $this;
  }

  public function optional()
  {
    if (!isset($this->rules[self::REQUIRED]) || $this->rules[self::REQUIRED] !== true)
      $this->rules[self::OPTIONAL] = true;
    return $this;
  }

  public function number()
  {
    $this->rules[self::NUMBER] = true;
    return $this;
  }

  /**
   * Is the content superior to $value
   * @param mixed $value
   */
  public function superior($value)
  {
    $this->rules[self::SUPERIOR] = $value;
    return $this;
  }

  /**
   * Is the content inferior to $value
   * @param mixed $value
   */
  public function inferior($value)
  {
    $this->rules[self::INFERIOR] = $value;
    return $this;
  }

  /**
   * Is the content equals to $value
   * @param mixed $value
   */
  public function equals($value)
  {
    $this->rules[self::EQUALS] = $value;
    return $this;
  }

  /**
   * Is the content superior or equals to $value
   * @param mixed $value
   */
  public function superior_or_equals($value)
  {
    $this->rules[self::SUPERIOR_OR_EQUALS] = $value;
    return $this;
  }

  /**
   * Is the content inferior or equals to $value
   * @param mixed $value
   */
  public function inferior_or_equals($value)
  {
    $this->rules[self::INFERIOR_OR_EQUALS] = $value;
    return $this;
  }

  /**
   * Checks if the content is inside the 
   * given array
   * @param array $things
   */
  public function in($things)
  {
    $this->rules[self::IN] = $things;
    return $this;
  }


  /**
   * Uses the value inside of result from $callable_for_value and checks using $callable_for_rule
   * The $callable_for_value can call $this->content
   * @param \Closure $callable_for_value
   * @param \Closure $callable_for_rule
   */
  public function inner_checking($callable_for_value, $callable_for_rule)
  {
    // $callable_for_value = $callable_for_value->bindTo($this, static::class);

    // $value = $callable_for_value();

    // $new_rule = (new OneValidation($value));

    // $callable_for_rule = $callable_for_rule->bindTo($new_rule, static::class);

    // $callable_for_rule();

    // if (!isset($this->rules[self::INNER_CHECKING]))
    //   $this->rules[self::INNER_CHECKING] = [];

    // if (!isset($this->rules[self::INNER_CHECKING][$value]))
    //   $this->rules[self::INNER_CHECKING][$value] = [];

    // $this->rules[self::INNER_CHECKING][$value] += $new_rule->rules;

    $callable_for_value = $callable_for_value->bindTo($this, static::class);
    $this->rules[self::INNER_CHECKING][] = [$callable_for_value, $callable_for_rule];
    // $value = $callable_for_value();
    // $new_rule = (new OneValidation($value));
    // $callable_for_rule = $callable_for_rule->bindTo($new_rule, static::class);

    return $this;
  }

  /**
   * Practical example of innerChecking()
   * Performs ruling over length of content
   * @param \Closure $callable_for_rule
   */
  public function length($callable_for_rule)
  {
    $callable_for_value = function () {
      return strlen($this->content);
    };
    return $this->inner_checking($callable_for_value, $callable_for_rule);
  }

  /**
   * Can only be used on array type validation
   * Gives the content of adjacent content
   * ['current_content' => $smth1, 'adjacent_content' => $smth2]
   * where 'current_content' is the Rule $this->content_name & $smth1 is the $this->content
   * @param string $content_name
   */
  public static function value_of($content_name): array
  {
    return ['type' => self::VALUEOF, 'content' => $content_name];
  }

  /**
   * Defines custom messages on error
   * With the key as the error constant type
   * @param array $messages
   */
  public function messages($messages)
  {
    $this->messages += $messages;
    return $this;
  }

  /**
   * Make a custom condition with a callback
   * The Rule can be accesed via $this
   * $name is used inside the $this->errors
   * @param \Closure $callback
   * @param string $name
   */
  public function other($callback, $name = 'other')
  {
    $this->rules[self::OTHER][$name] = $callback;
    return $this;
  }
}
