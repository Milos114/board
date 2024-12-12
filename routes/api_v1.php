<?php

use App\Http\Controllers\API\V1\LaneController;
use App\Http\Controllers\API\V1\PriorityController;
use App\Http\Controllers\API\V1\TicketController;
use App\Http\Controllers\API\V1\UserController;
use App\Http\Controllers\RegisterController;
use App\Http\Middleware\UserCreateMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {

    Route::get('me', [RegisterController::class, 'me']);

    // Users
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::post('/users', [UserController::class, 'store'])->name('users.store')->middleware(UserCreateMiddleware::class);
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update')->middleware('can:update,user');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('can:delete,user');

    // Tickets
    Route::apiResource('tickets', TicketController::class);

    // Lanes
    Route::apiResource('lanes', LaneController::class);

    // Priorities
    Route::apiResource('priorities', PriorityController::class);
});
