<?php

use compilers\html_php\HtmlCompiler;
use framework\components\route\Route;
use framework\rule\Rule;
use http\controllers\HphpController;

Route::get('/', function () {
  return view("welcome", [
    'time' => date("d/m/Y")
  ], HtmlCompiler::class);
})->name('index')->save();

Route::get('/hphp/<<testvalue>>', [HphpController::class, 'index'])->rules([
  Rule::from('testvalue')->required()->other(function () {
    return !is_numeric($this->content);
  })
])->name('hphp.index')->save();
