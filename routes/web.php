<?php

use Illuminate\Support\Facades\Route;

Route::get('/ping', fn () => 'Pong');

Route::get('/', function () {
    return view('welcome');
});
