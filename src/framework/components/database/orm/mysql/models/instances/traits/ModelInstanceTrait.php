<?php

namespace framework\components\database\orm\mysql\models\instances\traits;

use framework\components\database\orm\mysql\executers\MysqlQueryExecuter;
use framework\components\database\orm\mysql\models\instances\exceptions\ModelMethodException;
use framework\components\database\orm\mysql\queries\SortedQueryMaker;
use framework\components\database\orm\mysql\models\BaseModel;

trait ModelInstanceTrait
{
  /**
   * Saves the current model into the database
   * @return bool
   */
  public function save()
  {
    /** @var ?BaseModel $parent_model */
    $parent_model = $this->parent_model;
    if (is_null($parent_model)) {
      throw new ModelMethodException("The \$parent_model is required, but \$this->parent_model = NULL. " . var_export($parent_model, true));
    }
    $query = SortedQueryMaker::update_set($parent_model::$table, $this->attributes)->where_all($this->original_attributes);

    return MysqlQueryExecuter::run($query->decode_query());
  }

  /**
   * Deletes this instance from the database
   * @return bool
   */
  public function delete()
  {
    /** @var ?BaseModel $parent_model */
    $parent_model = $this->parent_model;
    if (is_null($parent_model)) {
      throw new ModelMethodException("The \$parent_model is required, but \$this->parent_model = NULL. " . var_export($parent_model, true));
    }
    $query = SortedQueryMaker::delete()->from($parent_model::$table)->where_all($this->original_attributes);

    return MysqlQueryExecuter::run($query->decode_query());
  }
}
