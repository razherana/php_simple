<?php

use framework\components\route\Route;
use framework\rule\Rule;

Route::get('/', function () {
  return "test" . ($this->request->getParameters['test'] ?? '');
})->name('index')->save();

Route::get('/<<var>>', function ($var) {
  return "test" . ($var);
})->name('index.var')->save();
