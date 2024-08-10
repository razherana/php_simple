<?php

namespace framework\rule\validation;

use framework\rule\Rule;
use framework\rule\validation\Validator;

class OneValidation extends Rule implements Validator
{
  /**
   * Contains all errors with key
   * the constants in Rule
   */
  public $errors = [];

  public function validate(): bool
  {
    foreach ($this->rules as $k => $v) {
      switch ($k) {
        case Rule::REQUIRED:
          if ($v === false || $this->required_validation()) break;
          $this->errors[Rule::REQUIRED] = $this->messages[Rule::REQUIRED] ?? true;
          break;
        case Rule::OPTIONAL:
          if ($this->optional_validation()) return true;
          break;
        case Rule::NUMBER:
          if ($this->number_validation()) break;
          $this->errors[Rule::NUMBER] = $this->messages[Rule::NUMBER] ?? true;
          break;
        case Rule::SUPERIOR:
          if ($this->superior_validation($v)) break;
          $this->errors[Rule::SUPERIOR] = $this->messages[Rule::SUPERIOR] ?? true;
          break;
        case Rule::INFERIOR:
          if ($this->inferior_validation($v)) break;
          $this->errors[Rule::INFERIOR] = $this->messages[Rule::INFERIOR] ?? true;
          break;
        case Rule::EQUALS:
          if ($this->equals_validation($v)) break;
          $this->errors[Rule::EQUALS] = $this->messages[Rule::EQUALS] ?? true;
          break;
        case Rule::SUPERIOR_OR_EQUALS:
          if ($this->superior_or_equals_validation($v)) break;
          $this->errors[Rule::SUPERIOR_OR_EQUALS] = $this->messages[Rule::SUPERIOR_OR_EQUALS] ?? true;
          break;
        case Rule::INFERIOR_OR_EQUALS:
          if ($this->inferior_or_equals_validation($v)) break;
          $this->errors[Rule::INFERIOR_OR_EQUALS] = $this->messages[Rule::INFERIOR_OR_EQUALS] ?? true;
          break;
        case Rule::INNER_CHECKING:
          $errors = $this->inner_checking_validation($v);
          if (!empty($errors)) foreach ($errors as $err_n => $err_v)
            $this->errors[Rule::INNER_CHECKING][$err_n] = $err_v;
          break;
        case Rule::IN:
          if ($this->in_validation($v)) break;
          $this->errors[Rule::IN] = $this->messages[Rule::IN] ?? true;
          break;
        case Rule::OTHER:
          $errors = $this->other_validation($v);
          if (!empty($errors)) foreach ($errors as $err_n => $err_v)
            $this->errors[Rule::OTHER][$err_n] = $this->messages[Rule::OTHER][$err_n] ?? true;
          break;
      }
    }
    return count($this->errors) == 0;
  }

  /**
   * Checks the content if exists
   */
  public function required_validation()
  {
    //CHECK_IF_ERROR:Possible error case if some objects contains nothing
    return !empty($this->content);
  }

  public function optional_validation()
  {
    return empty($this->content);
  }

  public function number_validation()
  {
    return is_numeric($this->content);
  }

  /**
   * Is the content superior to $value
   * @param mixed $value
   */
  public function superior_validation($value)
  {
    return $this->content > $value;
  }

  /**
   * Is the content inferior to $value
   * @param mixed $value
   */
  public function inferior_validation($value)
  {
    return $this->content < $value;
  }

  /**
   * Is the content equals to $value
   * @param mixed $value
   */
  public function equals_validation($value)
  {
    return $this->content == $value;
  }

  /**
   * Is the content superior or equals to $value
   * @param mixed $value
   */
  public function superior_or_equals_validation($value)
  {
    return $this->content >= $value;
  }

  /**
   * Is the content inferior or equals to $value
   * @param mixed $value
   */
  public function inferior_or_equals_validation($value)
  {
    return $this->content <= $value;
  }

  /**
   * Uses the value inside of result from $callable_for_value and checks using $callable_for_rule
   * The $callable_for_value can call $this->content
   * @param \Closure $callable_for_value
   * @param \Closure $callable_for_rule
   */
  public function inner_checking_validation($content)
  {
    $errors = [];
    /**
     * @var static $rule
     */
    foreach ($content as $arr) {
      [$callable_for_value, $callable_for_rule] = $arr;

      $value = $callable_for_value();
      $rule = (new OneValidation($value));
      $callable_for_rule = $callable_for_rule->bindTo($rule, static::class);
      $callable_for_rule();

      $rule->validate();

      if (!empty($rule->errors))
        $errors[$value] = $rule->errors;
    }

    return $errors;
  }

  public function length_validation($content)
  {
    return $this->inner_checking_validation($content);
  }

  /**
   * @param array $things
   */
  public function in_validation($things)
  {
    return in_array($this->content, $things);
  }

  /**
   * @param \Closure[] $all_callbacks
   */
  public function other_validation($all_callbacks)
  {
    $errors = [];

    foreach ($all_callbacks as $name => $closure)
      if (!$closure()) {
        $errors[$name] = false;
      }

    return $errors;
  }
}
