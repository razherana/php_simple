<?php

namespace framework\components\debug;

use framework\base\Component;
use framework\base\config\ConfigurableElement;
use ErrorException;

class Debug extends ConfigurableElement implements Component
{
  private $is_activated = false, $is_error_exception = false, $is_test = false;

  public function config_file(): string
  {
    return "debug";
  }

  public function execute()
  {
    if ($this->is_activated) {
      ini_set("display_errors", 1);
      error_reporting(E_ALL);
    }

    if ($this->is_error_exception) {
      // Convert the error into an ErrorException
      set_error_handler(function ($errno, $errstr, $errfile, $errline) {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
      }, E_ALL);
    }
  }

  public function run_test()
  {
    if ($this->is_test) {
      require_once(___DIR___ . '/resources/tests/' . $this->is_test . '.php');
    }
  }

  public function initialize()
  {
    $this->is_activated = $this->read_config('debug');
    $this->is_error_exception = $this->read_config('error_exception');
    $this->is_test = $this->read_config('test_file');
  }
}
