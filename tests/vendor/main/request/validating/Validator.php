<?php

namespace vendor\main\request\validating;

use vendor\main\request\Request;
use vendor\main\uri\Url;
use vendor\main\util\Json;
use vendor\main\util\Message;

class Validator
{
  private $datas;
  private $errors = [];

  private function __construct($datas = [])
  {
    $this->datas = $datas;
  }

  public static function data($datas)
  {
    return new self($datas);
  }

  public static function query($request)
  {
    return new self($request->query());
  }

  public static function post($request)
  {
    return new self($request->post());
  }

  /**
   * Validate with an array of 
   * @param Rule[] $rules
   * set $redirect_link_or_route = Json::class for json return
   */
  public function validate($rules, $redirect_link_or_route, $isRoute = false)
  {
    foreach ($rules as $rule) {
      if (($res = (new Validation($this->datas, $rule))->validate()) !== true && is_array($res)) {
        if ($res[0] === false) {
          $this->errors[$rule->field] = str_replace(
            '<!>',
            $rule->field,
            empty($rule->message) ? Validation::messages[$res[1]] : $rule->message[$res[1]]
          );
        }
      }
    }

    if (!empty($this->errors)) {
      if ($redirect_link_or_route === Json::class) {
        $res = ['type' => 'error'] + $this->setJsonErrors();
        echo json_encode($res);
        return false;
      } else {
        $this->setErrors();
        if ($isRoute) {
          return to_route($redirect_link_or_route);
        } else {
          return Url::redirect($redirect_link_or_route);
        }
      }
    }

    return $this->datas;
  }

  private function setErrors()
  {
    foreach ($this->errors as $k => $v) {
      Message::set($k, $v, 'error');
    }
  }

  private function setJsonErrors()
  {
    foreach ($this->errors as $k => $v) {
      return ['from' => $k, 'message' => $v];
    }
  }
}
