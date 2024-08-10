<?php

namespace framework\rule\validation;

interface Validator
{
  /**
   * Validate the rules
   */
  public function validate(): bool;

  /**
   * Checks the content if exists
   */
  public function required_validation();

  /**
   * Sets the content as optional
   */
  public function optional_validation();

  /**
   * The content must be a number
   */
  public function number_validation();

  /**
   * Is the content superior to $value
   * @param mixed $value
   */
  public function superior_validation($value);

  /**
   * Is the content inferior to $value
   * @param mixed $value
   */
  public function inferior_validation($value);

  /**
   * Is the content equals to $value
   * @param mixed $value
   */
  public function equals_validation($value);

  /**
   * Is the content superior or equals to $value
   * @param mixed $value
   */
  public function superior_or_equals_validation($value);

  /**
   * Is the content inferior or equals to $value
   * @param mixed $value
   */
  public function inferior_or_equals_validation($value);

  /**
   * Uses the value inside of result from $callable_for_value and checks using $callable_for_rule
   * The $callable_for_value can call $this->content
   * @param \Closure $callable_for_value
   * @param \Closure $callable_for_rule
   */
  public function inner_checking_validation($content);

  public function length_validation($content);

  /**
   * @param \Closure[] $all_callbacks
   */
  public function other_validation($all_callbacks);

  /**
   * @param array $things
   */
  public function in_validation($things);
}
