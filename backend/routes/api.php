<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->get('/auth/me', [AuthController::class, 'me']);

Route::middleware('auth:api')->post('/auth/logout', [AuthController::class, 'logout']);

Route::middleware('auth:api')->post('/products', [ProductController::class, 'store']);

Route::middleware('auth:api')->put('/products/{id}', [ProductController::class, 'update']);

Route::middleware('auth:api')->delete('/products/{id}', [ProductController::class, 'destroy']);

Route::middleware('auth:api')->post('/orders', [OrderController::class, 'store']);

Route::middleware('auth:api')->get('/orders', [OrderController::class, 'index']);

Route::middleware('auth:api')->post('/payments', [PaymentController::class, 'store']);
