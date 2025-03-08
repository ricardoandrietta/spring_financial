<?php

use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/{id}', [UserController::class, 'show'])->whereNumber('userId');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->whereNumber('userId');
    Route::patch('/users/{id}/add-points', [UserController::class, 'addPoints'])->whereNumber('userId');
    Route::patch('/users/{id}/subtract-points', [UserController::class, 'subtractPoints'])->whereNumber('userId');
});
