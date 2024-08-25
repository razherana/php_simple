<?php

namespace framework\components\database\orm\mysql\request;

use BadMethodCallException;
use ReflectionClass;

use framework\components\database\orm\mysql\exceptions\MysqlInsertIntoException;
use framework\components\database\orm\mysql\exceptions\MysqlGroupableElementException;

use framework\components\database\orm\mysql\models\relations\traits\RelationTrait;

use framework\components\database\orm\mysql\request\elements\InsertInto;

use framework\components\database\orm\mysql\request\interfaces\MysqlElement;
use framework\components\database\orm\mysql\request\interfaces\MysqlGroupableElement;

use framework\components\database\orm\mysql\traits\MysqlRequestTrait;

/**
 * Represents an abstract class who can form mysql queries
 */
abstract class MysqlQueryable
{
  /**
   * Contains the elements of the query
   * in order
   * @var MysqlElement[] $elements
   */
  public $elements = [];

  /**
   * Mode test or value getter
   * Set to true if to use on some mysql element 
   * @var bool $mode_test
   */
  public $mode_test = false;

  final protected function verify_query()
  {
    // If mode test, then check nothing
    if ($this->mode_test) return;

    // Verify if not a insert into query and followed by other things
    // Note: InsertInto query has only one element -> the InsertInto instance itself
    if (!empty($this->elements) && $this->elements[0] instanceof InsertInto && count($this->elements) > 1) {
      $other_query_types = $this->elements;
      unset($other_query_types[0]);

      foreach ($other_query_types as $k => $o) {
        $other_query_types[$k] = $o::class;
      }

      throw new MysqlInsertIntoException("InsertInto query is followed by other query types (" . implode(', ', $other_query_types) . ')');
    }

    // Verify if there is an insert into in the middle of query
    foreach (array_values($this->elements) as $k => $e) if ($e instanceof InsertInto && $k != 0)
      throw new MysqlInsertIntoException("InsertInto mysql query element in the middle of the query (Element num : " . ($k + 1) . ")");
  }

  /**
   * Decode group of query elements
   * @param array $array_query
   */
  final protected static function decode_array_query($array_query): string
  {
    // Verification
    if (!isset($array_query['type'])) {
      throw new MysqlGroupableElementException("This MysqlQueryable contains a random array");
    }
    // Checks the type if it is a class who implements MysqlElement 
    else if (!in_array(MysqlElement::class, (new ReflectionClass($array_query['type']))->getInterfaceNames())) {
      throw new MysqlGroupableElementException("This MysqlQueryable contains a group with an unknown type (" . $array_query['type'] . ")");
    }
    // Checks the type if it is a class who implements MysqlGroupableElement 
    else if (!in_array(MysqlGroupableElement::class, (new ReflectionClass($array_query['type']))->getInterfaceNames())) {
      throw new MysqlGroupableElementException("This MysqlElement contains a group but doesn't have a group_decoder");
    }

    return $array_query['type']::decode_group($array_query);
  }

  /**
   * Use these traits for __call and __callStatic
   * @return \ReflectionClass[]
   */
  protected static function use_traits(): array
  {
    return (new ReflectionClass(static::class))->getTraits();
  }

  public function __call($name, $arguments): static
  {
    // Checks for all traits if one of the trait has the method
    foreach (static::use_traits() as $name1 => $trait)
      // Checks the trait if it uses has MysqlRequestTrait
      if (in_array(MysqlRequestTrait::class, $trait->getTraitNames())) {
        // Checks the trait if it has the method
        if (method_exists($name1, $name . '_instance'))
          return $this->{$name . '_instance'}(...$arguments);
      }
    throw new BadMethodCallException("Undefined method " . $name);
  }

  public static function __callStatic($name, $arguments): static
  {
    // Checks for all traits if one of the trait has the method
    foreach (static::use_traits() as $name1 => $trait) {
      // Checks the trait if it uses has MysqlRequestTrait
      if (in_array(MysqlRequestTrait::class, $trait->getTraitNames())) {
        // Checks the trait if it has the method
        if (method_exists($name1, $name . '_static'))
          return static::{$name . '_static'}(...$arguments);
      }
    }
    throw new BadMethodCallException("Undefined static method " . $name);
  }

  /**
   * Search an element inside $this->elements
   * This method ignores group element
   * 
   * @param string[]|string $type The types of the element searched
   * @param ?\Closure $callable If the callable is defined, 
   * it uses that callable as a second comparator. The callable is binded with the element in question
   */
  final public function search_element($type, $callable = null): MysqlElement|false
  {
    if (is_string($type)) $type = [$type];

    if (is_null($callable)) $callable = function () {
      return true;
    };

    foreach ($this->elements as $e) {
      if (is_array($e)) continue;

      if (in_array($e::class, $type) && $callable->call($e)) {
        return $e;
      }
    }

    return false;
  }

  /**
   * Pushes the content of $query inside of the current query
   * @param static $query
   */
  public function push_query($query)
  {
    foreach ($query->elements as $e)
      $this->elements[] = $e;
    return $this;
  }

  /**
   * Decodes the query
   * @return string
   */
  public abstract function decode_query(): string;
}
