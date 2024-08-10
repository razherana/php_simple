<?php

namespace app\Singleton;

use vendor\main\cache\sessions\query\SessionModel;
use vendor\main\cache\sessions\Session;

class CsrfToken
{
  private static $csrf_token = null;

  public function __wakeup()
  {
  }

  public function __clone()
  {
  }

  private function __construct()
  {
  }

  public static function get()
  {
    if (self::$csrf_token == null) {
      $session_model = SessionModel::where('id_session', '=', getSessId())->get();

      if (count($session_model) <= 0) {
        Session::regenerate();
        return self::get();
      }

      self::$csrf_token = $session_model[0]->csrf_token;
    }
    return self::$csrf_token;
  }
}
