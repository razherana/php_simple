<?php

namespace http\controllers;

use compilers\html_php\HtmlCompiler;
use framework\http\handler\BaseController;

class HphpController extends BaseController
{

  public function index($testvalue)
  {
    return view('demo', [
      'test' => $this->request->getParameters['test'] ?? "Set the test get parameters to another value",
      "name" => $testvalue,
    ], HtmlCompiler::class);
  }
}
