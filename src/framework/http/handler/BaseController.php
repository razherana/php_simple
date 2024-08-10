<?php

namespace framework\http\handler;

use framework\http\Response;
use framework\http\Request;

class BaseController
{
  /**
   * Contains headers to send
   * when responding to
   * the client
   * @var array $headers
   */
  public $headers = [];

  /**
   * Contains the response instance
   * @var Response $response
   */
  public $response;

  /**
   * Contains the request for this controller
   * @var Request $request
   */
  public $request;

  /**
   * Defines that the show method is 'view'
   */
  public const VIEW_METHOD = 'view';

  /**
   * Defines that the show method is 'json'
   */
  public const JSON_METHOD = 'json';

  /**
   * Defines that the show method is 'plain'
   */
  public const PLAIN_METHOD = 'plain';

  /**
   * @var string $show_method
   */
  public $show_method = self::PLAIN_METHOD;

  /**
   * Sets the show_method to json
   */
  public function json()
  {
    $this->show_method = self::JSON_METHOD;
  }

  /**
   * Sets the show_method to plain
   */
  public function plain()
  {
    $this->show_method = self::PLAIN_METHOD;
  }

  /**
   * Sets the show_method to json
   */
  public function view()
  {
    $this->show_method = self::VIEW_METHOD;
  }

  /**
   * @param Request $request
   */
  public function __construct($request)
  {
    $this->request = $request;
    $this->response = new Response;
  }

  /**
   * Return the Response instance after
   * @param string $content
   */
  final public function get_response($content)
  {
    $response = $this->response;

    switch ($this->show_method) {
      case self::JSON_METHOD:
        $content = json_encode($content);
        break;
      case self::VIEW_METHOD:
        $content = file_get_contents($content);
    }

    $response->content = $content;
    $response->headers = $this->headers;

    return $response;
  }
}
