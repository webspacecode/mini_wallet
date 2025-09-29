<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;

// Auth Routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Transaction realeted routes
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/transactions', [TransactionController::class, 'makeTransaction']);
    
    Route::get('/transactions', [TransactionController::class, 'getTransactions']);

    Route::get('/users',  [UserController::class, 'getUsers']);

});




