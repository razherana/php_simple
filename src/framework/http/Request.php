<?php

namespace framework\http;

use Exception;
use framework\base\config\ConfigReader;

class Request
{
  /**
   * This variable contains the request uri usable by the router
   * If the app is in sub-folder it auto-removes it
   * @var string $request_uri
   */
  private $request_uri;

  /**
   * This method returns the $request_uri
   * @return string
   */
  public function request_uri(): string
  {
    return $this->request_uri;
  }

  /**
   * Initializes the request_uri
   */
  private function setRequestUri()
  {
    $uri = urldecode($this->server['REQUEST_URI']);

    if (ConfigReader::get('app', 'sub_folder') === true) {

      // From the server root 'localhost/__/__/public/index.php'

      $script_name = $this->server['SCRIPT_NAME'];

      $public_folder = ConfigReader::get('app', 'public_folder') ?? 'public';

      $script_name = str_replace("$public_folder/index.php", '', $script_name);

      $uri = str_replace($script_name, '', $uri);

      if ($uri === '') {
        $uri = "/";
      } else if ($uri[0] != "/") {
        $uri = '/' . $uri;
      }
    } else if ($this->server['SCRIPT_NAME'] != '/index.php')
      throw new Exception("This server is inside a sub-folder, please set 'sub_folder' in config/app.php to true", 1);

    $this->request_uri = strtok($uri, '?');
  }

  public function __construct(
    public $getParameters,
    public $postParameters,
    public $cookies,
    public $files,
    public $server,
  ) {
    $this->setRequestUri();
  }

  public static function getFromGlobalVars()
  {
    return new self($_GET, $_POST, $_COOKIE, $_FILES, $_SERVER);
  }

  public function method(): string
  {
    return $this->server['REQUEST_METHOD'];
  }
}
