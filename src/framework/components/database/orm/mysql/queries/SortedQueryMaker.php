<?php

namespace framework\components\database\orm\mysql\queries;

use framework\components\database\orm\mysql\exceptions\MysqlJoinException;
use framework\components\database\orm\mysql\exceptions\MysqlOnException;
use framework\components\database\orm\mysql\exceptions\MysqlQueryDecodingException;
use framework\components\database\orm\mysql\request\elements\Delete;
use framework\components\database\orm\mysql\request\elements\From;
use framework\components\database\orm\mysql\request\elements\InsertInto;
use framework\components\database\orm\mysql\request\elements\Join;
use framework\components\database\orm\mysql\request\elements\On;
use framework\components\database\orm\mysql\request\elements\Order;
use framework\components\database\orm\mysql\request\elements\Select;
use framework\components\database\orm\mysql\request\elements\UpdateSet;
use framework\components\database\orm\mysql\request\elements\Where;
use framework\components\database\orm\mysql\traits\DeleteTrait;
use framework\components\database\orm\mysql\traits\FromTrait;
use framework\components\database\orm\mysql\traits\InsertIntoTrait;
use framework\components\database\orm\mysql\traits\JoinTrait;
use framework\components\database\orm\mysql\traits\OnTrait;
use framework\components\database\orm\mysql\traits\OrderTrait;
use framework\components\database\orm\mysql\traits\SelectTrait;
use framework\components\database\orm\mysql\traits\UpdateSetTrait;
use framework\components\database\orm\mysql\traits\WhereTrait;

/**
 * Like DefaultQueryMaker but the mysql elements are sorted
 */
class SortedQueryMaker extends DefaultQueryMaker
{
  use SelectTrait, WhereTrait, FromTrait, OrderTrait, DeleteTrait, InsertIntoTrait, OnTrait, JoinTrait, UpdateSetTrait;

  /**
   * Contains the unordered $elements
   * @var ?array $old_elements 
   */
  private $old_elements = null;

  public function decode_query(): string
  {
    $this->old_elements = $this->elements;
    $this->elements = $this->sort_query();
    return parent::decode_query();
  }

  private function sort_query()
  {
    $wheres = [];
    $order_bys = [];

    /**
     * @var array<array<Join, On>>
     */
    $join_ons = [];

    $select = null;
    $delete = null;
    $insert_into = null;
    $from = null;
    $update_set = null;

    foreach ($this->elements as $e) {
      if (is_array($e)) switch ($e['type']) {
        case Where::class:
          $wheres[] = $e;
          break;
      }
      else switch ($e::class) {
        case Where::class:
          $wheres[] = $e;
          break;
        case Order::class:
          $order_bys[] = $e;
          break;
        case Join::class:
          $join_ons[] = [$e];
          break;
        case On::class:
          $join_ons[array_key_last($join_ons)][] = $e;
          break;
        case Select::class:
          $select = $e;
          break;
        case Delete::class:
          $delete = $e;
          break;
        case InsertInto::class:
          $insert_into = $e;
          break;
        case From::class:
          $from = $e;
          break;
        case UpdateSet::class:
          $update_set = $e;
          break;
      }
    }

    // Separate Join and On inside the same array
    $joins_divised = [];
    foreach ($join_ons as $j) {

      if (count($j) != 2) {
        if ($j[array_key_first($j)] instanceof Join)
          throw new MysqlJoinException("This join has no On conditions", $j[array_key_first($j)]);

        throw new MysqlOnException("This on condition has no Join mysql element", $j[array_key_first($j)]);
      }

      $joins_divised[] = $j[0];
      $joins_divised[] = $j[1];
    }

    // $select, $delete, $insert_into, $update_set
    // Only ONE should be NOT null
    $count = count($arr = array_filter([$select, $delete, $insert_into, $update_set], function ($e) {
      return $e !== null;
    }));

    // Gets the classname inside $arr (array_containing only the $main)
    $class_names = [];
    if ($count > 0) foreach ($arr as $v) $class_names[] = $v::class;

    if ($count > 1)
      throw new MysqlQueryDecodingException("This query has more than one main : [" . implode(' ,', $class_names) . "]", $this);
    if ($count <= 0)
      throw new MysqlQueryDecodingException("This query has no main", $this);

    $main = $arr[array_key_first($arr)];

    if ($insert_into !== null)
      return array($insert_into);
    else if ($update_set !== null)
      return array($update_set, ...$wheres);
    else
      return array($main, $from, ...$joins_divised, ...$wheres, ...$order_bys);
  }
}
