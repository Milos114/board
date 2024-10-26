<?php

use App\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Route;

Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [RegisterController::class, 'login']);

Route::get('test', static function () {
    return response()->json(['message' => 'Hello World!']);
});

Route::prefix('v1')->group(base_path('routes/api_v1.php'));

