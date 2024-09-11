<?php

namespace framework\components\session;

use framework\components\session\exceptions\SessionException;
use framework\components\session\interfaces\SessionInitializeInterface;
use framework\components\session\interfaces\SessionReservedKeywordsInterface;

class SessionManager implements SessionReservedKeywordsInterface, SessionInitializeInterface
{
  /**
   * If Admin mode is set to true, then no reserved keyword checking is done
   * @var bool $admin_mode
   */
  protected $admin_mode = false;

  /**
   * Contains the content of $_SESSION
   * @var array $session_data
   */
  public $session_data;

  /**
   * These are the reserved keywords for $_SESSION
   * We use a static var so every Component can add their own reserved keys
   * @var array $RESERVED_KEYS
   */
  public static $RESERVED_KEYS = [];

  /**
   * Construct a new SessionManager
   */
  public function __construct($admin_mode = false)
  {
    $this->session_data = &$_SESSION;
    $this->admin_mode = $admin_mode;
  }

  public static function get_session_reserved_keywords(): array
  {
    return [
      "___temp___",
      "___tempdelete___"
    ];
  }

  public static function initialize_session(): void
  {
    $session = new self(true);

    if (!$session->exists("___temp___")) {
      $session->set("___temp___", []);
      return;
    }

    if (!$session->exists("___tempdelete___")) {
      $session->set("___tempdelete___", []);
      return;
    }

    $tempdelete = $session->get('___tempdelete___');

    foreach ($tempdelete as $_tempdel) if ($session->temp_exists($_tempdel))
      $session->temp_destroy($_tempdel);

    $session->set("___tempdelete___", array_keys($session->get('___temp___')));
  }

  /**
   * Regenerates the id of the session
   */
  public static function regenerate($delete_old_session = true): bool
  {
    if (session_status() !== PHP_SESSION_ACTIVE)
      throw new SessionException("No session active, cannot regenerate");
    if (headers_sent())
      throw new SessionException("Headers have already been sent, cannot regenerate");

    return session_regenerate_id($delete_old_session);
  }

  /**
   * Remove the key from Session
   * If the key is reserved, an exception is thrown
   * If the key doesn't exist, an exception is thrown
   * @param string $key
   * @return bool If the key is destroyed, then __TRUE__ else __FALSE__
   */
  public function destroy($key): bool
  {
    if (!$this->admin_mode && in_array($key, $this::$RESERVED_KEYS))
      throw new SessionException("The key : $key is reserved");
    if (!$this->exists($key))
      throw new SessionException("The key : $key doesn't exist");

    unset($this->session_data[$key]);
    return !$this->exists($key);
  }

  /**
   * Sets a value to session
   * @param string $key
   * @param mixed $value
   * @return bool
   */
  public function set($key, $value): bool
  {
    if (!$this->admin_mode && in_array($key, $this::$RESERVED_KEYS))
      throw new SessionException("The key : $key is reserved");

    $this->session_data[$key] = $value;
    return $this->exists($key) && $this->session_data[$key] === $value;
  }

  /**
   * Sets a temporary value to session
   * It means that this value would be deleted after another request
   * @param string $key
   * @param mixed $value
   * @return bool
   */
  public function temp_set($key, $value): bool
  {
    $this->session_data['___temp___'][$key] = &$value;
    return $this->temp_exists($key) && $this->session_data['___temp___'][$key] === $value;
  }

  /**
   * Checks if the key exist in session
   * @param string $key
   * @return bool
   */
  public function exists($key): bool
  {
    return in_array($key, array_keys($this->session_data ?? []));
  }

  /**
   * Checks if the temp key exists
   * @param string $key
   * @return bool
   */
  public function temp_exists($key): bool
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
  public function get($key): mixed
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
  public function temp_get($key): mixed
  {
    if (!$this->temp_exists($key))
      throw new SessionException("This $key doesn't exist in temp session");

    return $this->session_data['___temp___'][$key];
  }

  /**
   * Destroy a temp value in session
   * If it doesn't exist, an exception is thrown
   * @param string $key
   * @return bool If the temp key is destroyed, then __TRUE__ else __FALSE__
   */
  public function temp_destroy($key): bool
  {
    if (!$this->temp_exists($key))
      throw new SessionException("The temp key : $key doesn't exist");

    unset($this->session_data["___temp___"][$key]);
    return !$this->temp_exists($key);
  }
}
