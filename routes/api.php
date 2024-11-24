<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Router;

Route::get('/ping', fn () => 'Pong');


Route::group(['prefix' => 'users'], function (Router $router) {
    $router->get('/', [\App\Http\Controllers\User\UserController::class, 'index'])->name('api.users.index');
});
