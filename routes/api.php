<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Router;

Route::get('/ping', fn () => 'Pong')->name('api.ping');

Route::group([
    'prefix' => 'auth',
], function (Router $router) {
    $router->post('login', [AuthController::class, 'login'])->name('api.auth.login');

    $router->group(['middleware' => 'auth:api'], function (Router $router) {
        $router->post('logout', [AuthController::class, 'logout'])->name('api.auth.logout');
        $router->post('refresh', [AuthController::class, 'refresh'])->name('api.auth.refresh');
        $router->post('me', [AuthController::class, 'me'])->name('api.auth.me');
    });
});

Route::group(['prefix' => 'users'], function (Router $router) {
    $router->get('/', [UserController::class, 'index'])->name('api.users.index');
});
