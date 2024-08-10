<?php

namespace vendor\main\util;

use vendor\main\cache\sessions\Session;

class Message
{
  public $type = "", $name = "", $message = "", $count = 0, $uuid;

  public function __construct($name, $message, $count, $type = 'default')
  {
    $this->type = $type;
    $this->name = $name;
    $this->message = $message;
    $this->count = $count;
    $this->uuid = bin2hex(random_bytes(8));
  }

  public static function set($name, $message, $type = 'default')
  {
    $ms = Session::get()->___msg___;
    $a = new Message($name, $message, count($ms), $type);
    $ms[$a->uuid] = $a;
    Session::set('___msg___', array_merge($ms));
  }

  public function delete()
  {
    $ms = Session::get()->___msg___;
    unset($ms[$this->uuid]);
    Session::set('___msg___', array_merge($ms));
  }

  public static function all()
  {
    return Session::get()->___msg___;
  }
}
