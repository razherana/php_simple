<?php

return '<?php

namespace app\Singleton;

class <<singleton_name>>
{
  /**
   * The var to store inside this singleton 
   */
  private static $<<singleton_var_name>> = null;

  public function __wakeup()
  {
  }

  public function __clone()
  {
  }

  /**
   * Not let the singleton to construct
   */
  private function __construct()
  {
  }

  /**
   * Get the var
   */
  public static function get()
  {
    if (self::$<<singleton_var_name>> === null) {
      // self::$<<singleton_var_name>> = [something];
    }
    return self::$<<singleton_var_name>>;
  }
}
';