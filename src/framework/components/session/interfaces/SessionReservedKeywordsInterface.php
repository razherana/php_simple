<?php

namespace framework\components\session\interfaces;

/**
 * This interface defines that a class has some reserved keywords for session
 */
interface SessionReservedKeywordsInterface
{
  /**
   * Returns the reserved keywords for session
   * @return string[] The reserved keywords 
   */
  public static function get_session_reserved_keywords(): array;
}
