<?php

namespace vendor\main\cache\sessions\singleton;

use vendor\main\cache\sessions\query\SessionModel;
use vendor\main\cache\sessions\Session;

class SessionId
{
  public static $ignore = false;

  private static $id = null;

  private function __construct()
  {
  }

  public static function id()
  {
    if (self::$id === null) {
      $result = SessionModel::where('id_session', '=', getSessId())->get();
      if (empty($result) || $result[0]->id_session === null) {
        // dd();
        Session::requestInitializeSession();
      }
      $result = SessionModel::where('id_session', '=', getSessId())->get();
      self::$id = $result[array_key_first($result)]->id_session_php;
    }
    return (string) self::$id;
  }

  public static function reset_id()
  {
    self::$id = null;
    $result = SessionModel::where('id_session', '=', getSessId())->get();
    if (empty($result) || $result[0]->id_session === null) {
      // dd();
      Session::requestInitializeSession();
    }
    $result = SessionModel::where('id_session', '=', getSessId())->get();
    self::$id = $result[array_key_first($result)]->id_session_php;
  }
}
