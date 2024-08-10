<?php

return "use app\\Http\\Controllers\\AuthController;

Route::get('/login', [AuthController::class, 'login'])->name('auth.login')->end();
Route::post('/login', [AuthController::class, 'do_login']);
Route::get('/register', [AuthController::class, 'register'])->name('auth.register')->end();
Route::post('/register', [AuthController::class, 'do_register']);
";
