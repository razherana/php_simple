<?php

namespace app\Models;

use vendor\main\database\model\BaseModel;

class User extends BaseModel
{
  /**
   * Table name
   */
  public static $table = "users";

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
  public static $hidden = ['image_file'];

  /**
   * Relations to auto use
   */
  public static $with = [];

  public function adopter()
  {
    return $this->hasMany(Animal::class, 'id', 'adopte');
  }

  public function image_file()
  {
    return $this->hasMany(File::class, 'id_file', 'id');
  }
}
