<?php

namespace framework\components\session\drivers;

use framework\components\session\exceptions\SessionFileDriverException;

class SessionFileDriver extends SessionDriver
{
  private $session_save_path;

  /**
   * @param string $key
   * @param string $session_save_path
   */
  public function __construct($key)
  {
    $this->key = $key;
  }

  public function open(string $path, string $name): bool
  {
    $this->session_save_path = $path;
    if (!is_dir($this->session_save_path))
      if (!mkdir($this->session_save_path, 0700, true))
        throw new SessionFileDriverException("Cannot create the session directory");

    return true;
  }

  public function close(): bool
  {
    return true;
  }

  public function read($id): string|false
  {
    $file = $this->session_save_path . "/sess_$id";

    if (!file_exists($file)) {
      return "";
    }

    // Get the content of the file
    $data = file_get_contents($file);

    // If false, cannot read the file
    if ($data === false) {
      throw new SessionFileDriverException("Cannot read the session_file");
    }

    return $this->decrypt($data);
  }

  public function write(string $id, string $data): bool
  {
    $file = $this->session_save_path . "/sess_$id";

    $encrypted_data = $this->encrypt($data);

    return file_put_contents($file, $encrypted_data) !== false;
  }

  public function destroy(string $id): bool
  {
    $file = $this->session_save_path . "/sess_$id";

    if (file_exists($file))
      return unlink($file);

    return true;
  }

  public function gc(int $max_lifetime): int|false
  {
    $count = 0;

    foreach (glob($this->session_save_path . "/sess_*") as $file)
      if (filemtime($file) + $max_lifetime < time()) if (unlink($file))
        $count++;

    return $count;
  }
}
