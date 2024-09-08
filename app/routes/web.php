<?php

use compilers\html_php\HtmlCompiler;
use framework\components\route\Route;
use framework\http\Response;
use framework\rule\Rule;

Route::get('/', function () {
  return view("test", [], HtmlCompiler::class);
})->name('index')->save();

Route::get('/test2', function () {
  return view("test2.test", [], HtmlCompiler::class);
});

Route::get('/users/<<var>>', function ($var) {
  $this->json();
  $data = [
    1 => "herana",
    2 => "fanilo",
    3 => "josoa",
    4 => "rotsy"
  ];

  if (isset($data[$var]))
    return $data[$var];

  return Response::abort(404);
})->name('index.var')->rules([
  Rule::from('var')->number()->superior(0)->in([1, 2, 3, 4])
])->save();
