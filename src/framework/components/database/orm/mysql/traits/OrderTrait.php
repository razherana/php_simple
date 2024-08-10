<?php

namespace framework\components\database\orm\mysql\traits;

use framework\components\database\orm\mysql\request\Order;

trait OrderTrait
{
  use MysqlRequestTrait;

  /**
   * @param string $column
   */
  public function order_by_desc($column)
  {
    $this->elements[] = new Order(Order::DESC, $column);
    return $this;
  }

  /**
   * @param string $column
   */
  public function order_by_asc($column)
  {
    $this->elements[] = new Order(Order::ASC, $column);
    return $this;
  }
}
