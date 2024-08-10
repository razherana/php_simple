<?php

namespace vendor\main\cache\sessions\query;

use vendor\main\database\model\BaseModel;

class SessionModel extends BaseModel
{
  /**
   * Table name
   */
  public static $table = "___sessions___";

  /**
   * Fillable in the Model
   */
  public static $fillable = ["*"];

  /**
   * Primary Key of the Model
   */
  public static $primaryKey = "id";

  /**
   * Hidden in the result
   */
  public static $hidden = [];

  /**
   * Relations to auto use
   */
  public static $with = [];
}
