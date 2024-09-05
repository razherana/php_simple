<?php

use framework\components\route\Route;
use framework\rule\Rule;

Route::get('/', function () {
  $this->plain();
  
  return '<!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
  </head>
  <body>
    
  </body>
  </html>';
})->name('index')->save();

Route::get('/<<var>>/<<var2>>', function ($var, $var2) {
  return "test " . ($var) . " aiza koa " . ($var2) . "<br>";
})->name('index.var')->rules([
  Rule::from('var')->number()
])->save();
