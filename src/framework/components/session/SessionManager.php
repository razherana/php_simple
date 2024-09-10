<?php

namespace framework\components\session;

use framework\components\session\exceptions\SessionException;

class SessionManager
{
  /**
   * Contains the content of $_SESSION
   * @var array $session_data
   */
  protected $session_data;

  /**
   * These are the reserved keywords for $_SESSION
   * We use a static var so every Component can add their own reserved keys
   * @var array $RESERVED_KEYS
   */
  public static $RESERVED_KEYS = [
    "___temp___"
  ];

  /**
   * Construct a new SessionManager
   */
  public function __construct()
  {
    $this->session_data = &$_SESSION;
  }

  /**
   * Remove the key from Session
   * @param string $key
   * @return bool
   */
  public function destroy($key): bool
  {
    if (in_array($key, $this::$RESERVED_KEYS))
      throw new SessionException("The key : $key is reserved");

    unset($this->session_data[$key]);
    return !$this->exists($key);
  }

  /**
   * Sets a value to session
   * @param string $key
   * @param mixed &$value
   * @return bool
   */
  public function set($key, &$value): bool
  {
    if (in_array($key, $this::$RESERVED_KEYS))
      throw new SessionException("The key : $key is reserved");

    $this->session_data[$key] = &$value;
    return $this->exists($key) && $this->session_data[$key] === $value;
  }

  /**
   * Sets a temporary value to session
   * It means that this value would be deleted after another request
   * @param string $key
   * @param mixed &$value
   * @return bool
   */
  public function temp($key, &$value): bool
  {
    $this->session_data['___temp___'][$key] = &$value;
    return $this->temp_exists($key) && $this->session_data['___temp___'][$key] === $value;
  }

  /**
   * Checks if the key exist in session
   * @param string $key
   * @return bool
   */
  public function exists($key)
  {
    return in_array($key, array_keys($this->session_data ?? []));
  }

  /**
   * Checks if the temp key exists
   * @param string $key
   * @return bool
   */
  public function temp_exists($key)
  {
    return $this->exists("___temp___")
      && in_array($key, array_keys($this->session_data['___temp___'] ?? []));
  }

  /**
   * Gets a value in session
   * If the value doesn't exist, an exception is thrown
   * @param string $key
   * @return mixed 
   */
  public function get($key)
  {
    if (!$this->exists($key))
      throw new SessionException("This $key doesn't exist in session");

    return $this->session_data[$key];
  }

  /**
   * Gets a value in temp session
   * If the value doesn't exist, an exception is thrown
   * @param string $key
   * @return mixed 
   */
  public function temp_get($key)
  {
    if (!$this->temp_exists($key))
      throw new SessionException("This $key doesn't exist in temp session");

    return $this->session_data['___temp___'][$key];
  }
}
