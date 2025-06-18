<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
use App\Http\Controllers\TelegramOrderController;


use App\Http\Controllers\Api\UserController;
use  App\Http\Controllers\Api\ProductController;


Route::apiResource('users', UserController::class);
Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{id}', [UserController::class, 'show']);



// product


Route::apiResource('/products', ProductController::class);
Route::get('/backendproducts', [ProductController::class, 'index']);
Route::post('/products', [ProductController::class, 'store']);



// Telegram order


Route::post('/send-order', [TelegramOrderController::class, 'send']);
