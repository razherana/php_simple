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
    if (is_string($this->is_test)) {
      require_once(___DIR___ . '/resources/tests/' . $this->is_test . '.php');
    } elseif (is_array($this->is_test)) foreach ($this->is_test as $test) {
      require_once(___DIR___ . '/resources/tests/' . $test . '.php');
    }
  }

  public function initialize()
  {
    $this->is_activated = $this->read_cached_config('debug');
    $this->is_error_exception = $this->read_cached_config('error_exception');
    $this->is_test = $this->read_cached_config('test_file');
  }
}
