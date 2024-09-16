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
   * Contains parent folder, made it static to cache the value
   * @var ?string $parent_folder
   */
  public static $parent_folder = null;

  /**
   * This method returns the $request_uri
   * @return string
   */
  public function request_uri(): string
  {
    // Added ?? '' because when it may be null in php cli 
    return $this->request_uri ?? '';
  }

  /**
   * When setted to true, this will ignore all checking in default request
   * Typically useful in cli php
   * @var bool $ignore
   */
  public static $ignore = false;

  /**
   * Initializes the request_uri
   */
  private function set_request_uri()
  {
    if (self::$ignore) return;

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

    if (!$this::$parent_folder)
      $this::$parent_folder = $this->get_parent_uri();
  }

  /**
   * Get the parent folders of the application
   * If not a subfolder app then /, else some string handling is done
   * @return string
   */
  public function get_parent_uri()
  {
    if (ConfigReader::get('app', 'sub_folder') !== true)
      return '/';

    // From the server root 'localhost/__/__/public/index.php'

    $script_name = $this->server['SCRIPT_NAME'];

    $public_folder = ConfigReader::get('app', 'public_folder') ?? 'public';

    return str_replace("$public_folder/index.php", '', $script_name);
  }

  public function __construct(
    public $getParameters,
    public $postParameters,
    public $cookies,
    public $files,
    public $server,
  ) {
    $this->set_request_uri();
  }

  public static function get_from_global_vars()
  {
    return new self($_GET, $_POST, $_COOKIE, $_FILES, $_SERVER);
  }

  public function method(): string
  {
    return $this->server['REQUEST_METHOD'];
  }
}
