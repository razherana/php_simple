<?php

namespace models;

use framework\components\database\orm\mysql\models\BaseModel;

class template_model_name extends BaseModel
{
  public static $table = 'template_model_table';

  public static $primary_key = 'template_model_primary_key';

  public static $with = [];

  protected static $fillable = null;

  protected static $non_fillable = '*';

  //
}
