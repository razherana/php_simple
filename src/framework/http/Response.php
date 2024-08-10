<?php

namespace framework\http;

class Response
{
  /**
   * @var string|\Stringable $content
   */
  public $content;

  /**
   * @var int $status_code
   */
  public $status_code = 200;

  /**
   * @var array $headers
   */
  public $headers = [];

  /**
   * Constructs a Response instance 
   * @param string|\Stringable $content
   */
  public function __construct($content = "", $status_code = 200, $headers = [])
  {
    $this->content = $content;
    $this->status_code = $status_code;
    $this->headers = $headers;
  }

  /**
   * Sends the Response instance to the client
   */
  public function send(): void
  {
    http_response_code($this->status_code);

    foreach ($this->headers as $k => $v)
      header("$k: $v");

    echo $this->content;
  }

  public static function abort(int $code)
  {
    $resp = new self(
      file_get_contents(___DIR___ . "/src/default_pages/$code.html"),
      $code,
    );
    $resp->send();
    die;
  }
}
