<?php

namespace vendor\main\database\model\auth;

use Exception;
use vendor\main\cache\sessions\Session;
use vendor\main\database\queries\QueryMaker;
use vendor\main\database\queries\QueryResult;
use vendor\main\Facades;
use vendor\main\util\Config;

class Auth extends Facades
{
  public $model, $id, $pass, $session_name = "___user___";

  public static $functions = [
    "attempt_" => "attempt",
    "checkIfExist_" => "checkIfExist",
    "logout_" => "logout",
    "register_" => "register",
    "user_" => "user",
    "loggedIn_" => "loggedIn",
  ];

  public static function __callStatic($name, $arguments)
  {
    if (in_array($name, array_values(self::$functions))) {
      return (new self())->{$name . "_"}(...$arguments);
    }
    throw new \Exception("Static Method doesn't exist", 1);
  }

  public function __construct($model = null, $id = null, $pass = null, $session_name = null)
  {
    if ($model == null) {
      $this->model = Config::get('auth', 'model');
      $this->id = Config::get('auth', 'auth_id');
      $this->pass = Config::get('auth', 'auth_pass');
    } else {
      $this->model = $model;
      $this->id = $id;
      $this->pass = $pass;
      $this->session_name = $session_name;
    }
  }

  /**
   * Attempt the user to login and registers him into the session
   * $data = [
   *  auth_id => value,
   *  auth_pass => value
   * ]
   */
  public function attempt_($data)
  {
    $pass = $data[$this->pass];

    $res = QueryResult::getArray(
      QueryMaker::model($this->model)
        ->select($this->model)
        ->where($this->id, '=', $data[$this->id])
    );

    if (empty($res)) return false;

    $res = $res[array_key_first($res)];

    if (password_verify($pass, $res->{$this->pass})) {
      Session::save($this->session_name, $res->{$res->modelName::$primaryKey});
      return true;
    }

    return false;
  }

  private function checkIfExist_($data)
  {
    return !empty(QueryResult::getArray(
      QueryMaker::model($this->model)
        ->select(['*'])
        ->where($this->id, '=', $data[$this->id])
    ));
  }

  public function logout_()
  {
    Session::delete($this->session_name);
  }

  public static function hashPass($pass)
  {
    return password_hash($pass, PASSWORD_BCRYPT);
  }

  public function register_($data)
  {
    $model = $this->model;

    if ($this->checkIfExist_($data)) {
      return false;
    }

    $data[$this->pass] = self::hashPass($data[$this->pass]);

    return QueryResult::execute(QueryMaker::model($model)->insertInto($data));
  }

  public function user_()
  {
    if (Session::get()->{$this->session_name} === null) {
      throw new \Exception("The User isn't logged In.", 1);
    }
    if (Session::get()->{"temp_user" . $this->session_name} === null) {
      Session::save('temp_user' . $this->session_name, ($this->model)::find(Session::get()->{$this->session_name}));
    }
    return Session::get()->{"temp_user" . $this->session_name};
  }

  public function loggedIn_()
  {
    return !(Session::get()->{$this->session_name} === null);
  }
}
