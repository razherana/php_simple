<?php

namespace framework\components\session\interfaces;

/**
 * This interface defines that a class needs to be initialized session 
 */
interface SessionInitializeInterface
{
  /**
   * Initilializes the session
   * @return void 
   */
  public static function initialize_session(): void;
}
