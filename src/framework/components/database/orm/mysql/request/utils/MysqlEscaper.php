<?php

namespace framework\components\database\orm\mysql\request\utils;

use framework\components\database\connection\MySqlConnection;

class MysqlEscaper
{
  protected const KEYS = ["NOW(", "DEFAULT", "HEX(", "UNHEX("];

  /**
   * Checks if a string is a mysql keyword
   * @param string $string
   */
  public static function is_mysql_special($string): bool
  {
    foreach (self::KEYS as $v) if (stripos($string, $v) !== false)
      return true;
    return false;
  }

  /**
   * Default clean and add quotes
   * @param string|float|int $content
   */
  public static function clean_and_add_quotes($content): string
  {
    if ($content == null) return "NULL";

    if (MysqlEscaper::is_mysql_special($content))
      return $content;

    $is_string = false;
    if (is_string($content)) $is_string = true;

    $content = MySqlConnection::get()->real_escape_string($content);

    if ($is_string)
      $content = '"' . $content . '"';

    return $content;
  }

  /**
   * Simple mysqli_real_escape_string if not in mysql keywords
   * @param string|int|float $content 
   */
  public static function clean_only($content)
  {
    if ($content == null) return "NULL";

    if (!MysqlEscaper::is_mysql_special($content))
      $content = MySqlConnection::get()->real_escape_string($content);
    return $content;
  }

  /**
   * Makes into a string "NULL" if the $content is NULL
   */
  public static function change_if_null($content)
  {
    if ($content === null) return "NULL";
    return $content;
  }
}
