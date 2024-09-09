<?php

namespace framework\components\database\orm\mysql\models\instances;

use ArrayAccess;
use framework\base\exceptions\DefaultException;

class DefaultModelInstance implements ArrayAccess
{
  /**
   * @var array $attributes
   */
  private $attributes = [];

  /**
   * @var array $originalAttributes
   */
  private $originalAttributes = [];

  public function offsetGet($offset): mixed
  {
    if (!$this->offsetExists($offset)) {
      throw new DefaultException("'$offset' doesn't exist in this ModelInstance");
    }

    return $this->attributes[$offset];
  }

  public function offsetSet($offset, $value): void
  {
    $this->attributes[$offset] = $value;
  }

  public function offsetExists($offset): bool
  {
    return (is_string($offset) || is_int($offset)) && in_array($offset, array_keys($this->attributes ?? []));
  }

  public function offsetUnset($offset): void
  {
    if (!$this->offsetExists($offset)) {
      throw new DefaultException("'$offset' doesn't exist in this ModelInstance");
    }

    unset($this->attributes[$offset]);
  }

  public function __get($name)
  {
    return $this->offsetGet($name);
  }

  public function __set($name, $value)
  {
    return $this->offsetSet($name, $value);
  }

  /**
   * @param array $attributes
   */
  public function __construct($attributes)
  {
    $this->attributes = $attributes;
    $this->originalAttributes = $attributes;
  }
}
