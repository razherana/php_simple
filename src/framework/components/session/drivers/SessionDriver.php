<?php

namespace framework\components\session\drivers;

use SessionHandlerInterface;

abstract class SessionDriver implements SessionHandlerInterface
{

  protected $key;

  protected function encrypt($data)
  {
    return gzencode(openssl_encrypt(
      $data,
      "AES-256-CBC",
      $this->key,
      0,
      substr(hash('sha256', $this->key, true), 0, 16)
    ));
  }

  protected function decrypt($data)
  {
    return openssl_decrypt(
      gzdecode($data),
      "AES-256-CBC",
      $this->key,
      0,
      substr(hash('sha256', $this->key, true), 0, 16)
    );
  }
}
