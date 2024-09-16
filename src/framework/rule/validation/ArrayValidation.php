<?php

namespace framework\rule\validation;

use framework\rule\Rule;
use framework\rule\validation\Validator;

class ArrayValidation implements Validator
{
  /**
   * The data to check from
   * @var array $data
   */
  protected $data = [];

  /**
   * Contains all errors with key
   * the constants in Rule
   */
  public $errors = [];

  /**
   * Changes every rule or inner rule
   * @var mixed $content
   */
  private $content;

  /**
   * Changes every rule
   * @var Rule $rule
   */
  private $rule;

  /**
   * Contains all the rules
   * @var Rule[] $rules
   */
  private $rules;

  /**
   * @param array $data
   * @param Rule[] $rules
   */
  public function __construct($data, $rules, $clone = false)
  {
    $this->data = $data;
    $new_rules = [];

    foreach ($rules as $rule) {
      // Clone the rule so it's not modified when saving the route in storage
      if ($clone)
        $rule = clone $rule;

      // Sets the content_name to the content
      $rule->content_name = $rule->content;

      // Sets the content to the content in the array data
      $rule->content = $data[$rule->content_name] ?? null;

      // Adds the cloned rule to new_rules
      if ($clone)
        $new_rules[] = $rule;
    }

    $this->rules = $clone ? $new_rules : $rules;
  }

  /**
   * Makes a new ArrayValidation instance
   * @param array $data
   * @param Rule[] $rules
   */
  public static function from($data, $rules, $clone = false)
  {
    return new self($data, $rules, $clone);
  }

  public function validate(): bool
  {
    foreach ($this->rules as $rule) {
      $this->content = $this->data[$rule->content_name] ?? false;
      $this->rule = $rule;
      foreach ($rule->rules as $k => $v) {
        switch ($k) {
          case Rule::REQUIRED:
            if ($v === false || $this->required_validation()) break;
            $this->errors[$rule->content_name][Rule::REQUIRED] = $rule->messages[Rule::REQUIRED] ?? true;
            break;
          case Rule::OPTIONAL:
            if ($this->optional_validation()) return true;
            break;
          case Rule::NUMBER:
            if ($this->number_validation()) break;
            $this->errors[$rule->content_name][Rule::NUMBER] = $rule->messages[Rule::NUMBER] ?? true;
            break;
          case Rule::SUPERIOR:
            if ($this->superior_validation($v)) break;
            $this->errors[$rule->content_name][Rule::SUPERIOR] = $rule->messages[Rule::SUPERIOR] ?? true;
            break;
          case Rule::INFERIOR:
            if ($this->inferior_validation($v)) break;
            $this->errors[$rule->content_name][Rule::INFERIOR] = $rule->messages[Rule::INFERIOR] ?? true;
            break;
          case Rule::EQUALS:
            if ($this->equals_validation($v)) break;
            $this->errors[$rule->content_name][Rule::EQUALS] = $rule->messages[Rule::EQUALS] ?? true;
            break;
          case Rule::SUPERIOR_OR_EQUALS:
            if ($this->superior_or_equals_validation($v)) break;
            $this->errors[$rule->content_name][Rule::SUPERIOR_OR_EQUALS] = $rule->messages[Rule::SUPERIOR_OR_EQUALS] ?? true;
            break;
          case Rule::INFERIOR_OR_EQUALS:
            if ($this->inferior_or_equals_validation($v)) break;
            $this->errors[$rule->content_name][Rule::INFERIOR_OR_EQUALS] = $rule->messages[Rule::INFERIOR_OR_EQUALS] ?? true;
            break;
          case Rule::INNER_CHECKING:
            $errors = $this->inner_checking_validation($v);
            if (!empty($errors)) foreach ($errors as $err_n => $err_v)
              $this->errors[$rule->content_name][Rule::INNER_CHECKING][$err_n] = $err_v;
            break;
          case Rule::IN:
            if ($this->in_validation($v)) break;
            $this->errors[$rule->content_name][Rule::IN] = $rule->messages[Rule::IN] ?? true;
            break;
          case Rule::OTHER:
            $errors = $this->other_validation($v);
            if (!empty($errors)) foreach ($errors as $err_n => $err_v)
              $this->errors[$rule->content_name][Rule::OTHER][$err_n] = $rule->messages[Rule::OTHER][$err_n] ?? true;
            break;
        }
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
      if (!$closure->call($this->rule)) {
        $errors[$name] = false;
      }

    return $errors;
  }
}
