<?php

use framework\components\route\Route;

Route::get('/', function () {
  return "test" . ($this->request->getParameters['test'] ?? '');
})->name('index')->save();

Route::get('/<<var>>/<<var2>>', function ($var, $var2) {
  return "test " . ($var) . " aiza koa " . ($var2) . "<br>";
})->name('index.var')->save();
