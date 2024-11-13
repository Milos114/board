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
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
    Route::put('/tickets/{ticket}', [TicketController::class, 'update'])->name('tickets.update');
    Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy'])->name('tickets.destroy');

    // Lanes
    Route::get('/lanes', [LaneController::class, 'index'])->name('states.index');
    Route::post('/lanes', [LaneController::class, 'store'])->name('states.store');
    Route::get('/lanes/{lane}', [LaneController::class, 'show'])->name('states.show');
    Route::put('/lanes/{lane}', [LaneController::class, 'update'])->name('states.update');
    Route::delete('/lanes/{lane}', [LaneController::class, 'destroy'])->name('states.destroy');

    // Priorities
    Route::get('/priorities', [PriorityController::class, 'index'])->name('priorities.index');
    Route::post('/priorities', [PriorityController::class, 'store'])->name('priorities.store');
    Route::put('/priorities/{priority}', [PriorityController::class, 'update'])->name('priorities.update');
    Route::get('/priorities/{priority}', [PriorityController::class, 'show'])->name('priorities.show');
    Route::delete('/priorities/{priority}', [PriorityController::class, 'destroy'])->name('priorities.destroy');
});
