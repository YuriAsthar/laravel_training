<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\TravelRequest\TravelRequestController;
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
        $router->get('me', [AuthController::class, 'me'])->name('api.auth.me');
    });
});

Route::group(['middleware' => 'auth:api'], function (Router $router) {
    $router->group(['prefix' => 'users'], function (Router $router) {
        $router->get('/', [UserController::class, 'index'])->name('api.users.index');
    });

    $router->group(['prefix' => 'travel-requests'], function (Router $router) {
        $router->get('/', [TravelRequestController::class, 'index'])->name('api.travel-requests.index');
        $router->post('/', [TravelRequestController::class, 'store'])->name('api.travel-requests.store');

        $router->group(['prefix' => '{travelRequest}'], function (Router $router) {
            $router->get('/', [TravelRequestController::class, 'show'])->name('api.travel-requests.show');
            $router->put('/', [TravelRequestController::class, 'update'])->name('api.travel-requests.update');
            $router->delete('/', [TravelRequestController::class, 'delete'])->name('api.travel-requests.delete');
        });
    });
});


