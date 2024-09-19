<?php

namespace framework\http;

use framework\base\config\ConfigurableElement;
use framework\components\session\interfaces\SessionInitializeInterface;
use framework\components\session\interfaces\SessionReservedKeywordsInterface;
use framework\components\session\SessionManager;

class Csrf extends ConfigurableElement implements
  SessionInitializeInterface,
  SessionReservedKeywordsInterface
{

  public static $csrf = null;

  public function config_file(): string
  {
    return 'csrf';
  }

  public static function initialize_session(): void
  {
    $session = new SessionManager(true);
    $key = (new self)->read_cached_config('session_keyword');

    if (!$session->exists($key))
      $session->set($key,  static::$csrf = base64_encode(random_bytes(64)));
    else
      static::$csrf = $session->get($key);
  }

  public static function get_session_reserved_keywords(): array
  {
    return [
      (new self)->read_cached_config('session_keyword')
    ];
  }
}
