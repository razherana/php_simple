<?php
return
  '<?php

namespace app\Models;

use vendor\main\database\model\BaseModel;

class <<model_name>> extends BaseModel
{
  /**
   * Table name
   */
  public static $table = "<<model_table>>";

  /**
   * Fillable in the Model
   */
  public static $fillable = ["*"];

  /**
   * Primary Key of the Model
   */
  public static $primaryKey = "<<model_primary_key>>";

  /**
   * Hidden in the result
   */
  public static $hidden = [];

  /**
   * Relations to auto use
   */
  public static $with = [];
}
';
