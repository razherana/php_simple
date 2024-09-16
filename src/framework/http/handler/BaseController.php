<?php

namespace framework\http\handler;

use framework\components\session\SessionManager;
use framework\http\Response;
use framework\http\Request;
use framework\view\comm\ViewElement;

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
   * Contains a session manager
   * @var SessionManager $session
   */
  public $session;

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
    $this->headers['Content-Type'] = "json";
  }

  /**
   * Sets the show_method to plain
   */
  public function plain()
  {
    $this->show_method = self::PLAIN_METHOD;
    $this->headers['Content-Type'] = "text";
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
    $this->session = new SessionManager;
    $this->request = $request;
    $this->response = new Response;
  }

  /**
   * Return the Response instance after
   * @param string|ViewElement $content
   */
  final public function get_response($content)
  {
    $response = $this->response;

    if ($content instanceof ViewElement)
      $content = $content->content;
    elseif ($this->show_method != self::PLAIN_METHOD)
      $content = json_encode($content);

    $response->content = $content;
    $response->headers = $this->headers;

    return $response;
  }
}
