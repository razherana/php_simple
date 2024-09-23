<?php

namespace framework\components\storage;

use framework\base\Component;
use framework\base\config\ConfigurableElement;

class Storage extends ConfigurableElement implements Component
{
  public function config_file(): string
  {
    return 'storage';
  }

  public function initialize()
  {
    // Initialize ini vars
    ini_set('upload_tmp_dir', $this->read_cached_config('upload'));
  }

  public function execute() {}
}
