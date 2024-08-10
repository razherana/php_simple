<?php

namespace app\Models;

use vendor\main\database\model\BaseModel;
use vendor\main\database\model\ModelInstance;

class File extends BaseModel
{
  public const UPLOADS_FOLDER = ___DIR___ . '/storage/uploads/',
    NOT_PUBLIC_OWNER = 'NULL',
    PUBLIC_OWNER = 0;

  /**
   * Table name
   */
  public static $table = "files";

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

  /**
   * Get the model file
   */
  public static function getFile(string $name)
  {
    $file = File::where('name', '=', $name)->get();
    if (empty($file)) {
      return false;
    }
    return $file[0];
  }
}
