<?php

use compilers\php\PhpCompiler;
use framework\components\route\Route;
use framework\rule\Rule;
use http\controllers\HphpController;

Route::get('/', function () {
  return view("welcome", [
    'time' => date("d/m/Y")
  ], PhpCompiler::class);
})->name('index')->save();

Route::get('/hphp/<<testvalue>>', [HphpController::class, 'index'])->rules([
  Rule::from('testvalue')->required()->other(function () {
    return !is_numeric($this->content);
  })
])->name('hphp.index')->save();
