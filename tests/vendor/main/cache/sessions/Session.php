<?php

namespace vendor\main\cache\sessions;

use DateTime;
use vendor\main\cache\sessions\query\SessionModel;
use vendor\main\cache\sessions\singleton\SessionId;
use vendor\main\Facades;
use vendor\main\util\RandomGenerator;

/**
 * Adding the id to the name of the session to prevent other app accessing the same session
 */
class Session extends Facades
{
  public static $functions = [
    "regenerate" => "regenerateFacade",
    "get" => "getFacade",
    "save" => "saveFacade",
    "delete" => "deleteFacade",
    "all" => "allFacade"
  ];

  public static function setLastActivity()
  {
    $res = SessionModel::where('id_session', '=', getSessId())->get();
    if (empty($res)) {
      self::initializeSession();
      return self::setLastActivity();
    }
    $res = $res[0];
    $diff = - (new Datetime($res->last_activity))->getTimestamp() + (new DateTime('now'))->getTimestamp();
    if ($diff >= 1800 || is_numeric($res->id_session)) {
      return Session::regenerate();
    }
    $res->last_activity = (new DateTime('now'))->format('Y-m-d H:i:s');
    $res->save();
  }

  public static function ignore($boolean = true)
  {
    SessionId::$ignore = $boolean;
  }

  public static function isIgnore()
  {
    return SessionId::$ignore;
  }

  public static function getSessId()
  {
    if (!session_id()) {
      header('location: .');
    }
    return session_id();
  }

  public function __construct()
  {
    if (self::isIgnore()) return;
    if (self::getMySession() === false) {
      self::setMySession();
    }
  }

  private static function getMySession()
  {
    if (self::isIgnore()) return [];
    $id = SessionId::id();
    if (!isset($_SESSION[$id])) $_SESSION[$id] = [];
    return $_SESSION[$id];
  }

  public static function requestInitializeSession()
  {
    self::initializeSession();
  }

  private static function deleteOldSession($old_id)
  {
    unset($_SESSION[$old_id]);
  }

  private static function initializeSession()
  {
    $result = SessionModel::where('id_session', '=', getSessId())->get();
    if (empty($result) || $result[0]->id_session === NULL) {
      $id_session = getSessId();
      do {
        $id_table = RandomGenerator::randomString(8);
      } while (is_numeric($id_table));

      SessionModel::create(['id_session' => $id_session, 'id_session_php' => $id_table, 'csrf_token' => RandomGenerator::randomString(32)]);
      $_SESSION[self::getUserSessionId()] = [];
    }
  }

  private static function setMySession($arr = [])
  {
    $_SESSION[self::getUserSessionId()] = $arr;
  }

  public static function getUserSessionId()
  {
    return SessionId::id();
  }

  public function regenerateFacade()
  {
    if (self::isIgnore()) return;
    $val = self::getMySession();

    $old_id = self::getUserSessionId();

    session_regenerate_id(true);
    session_write_close();
    session_start();

    SessionId::reset_id();
    self::deleteOldSession($old_id);
    self::requestInitializeSession();

    self::setMySession($val);
    return true;
  }

  public function saveFacade($all)
  {
    if (self::isIgnore()) return;
    $name = $all[0];
    $value = $all[1];
    $val = self::getMySession();
    $val[$name] = $value;
    self::setMySession($val);
  }

  public function allFacade()
  {
    return self::getMySession();
  }

  public function __get($name)
  {
    $a = self::getMySession();
    if (in_array($name, array_keys($a))) {
      return $a[$name];
    }
    return null;
  }

  public static function set($name, $value)
  {
    if (self::isIgnore()) return;
    $a = self::getMySession();
    $a[$name] = $value;
    self::setMySession($a);
  }

  public function getFacade()
  {
    return new self;
  }

  public function deleteFacade($all)
  {
    if (self::isIgnore()) return;
    $name = $all[0];
    $a = self::getMySession();
    if (!isset($a[$name]))
      return;
    unset($a[$name]);
    self::setMySession($a);
  }

  public static function destroy()
  {
    if (self::isIgnore()) return;
    session_destroy();
    session_start();
  }
}
