<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoriesApiController;

Route::get('/categories', [CategoriesApiController::class, 'show']);
Route::get('/categories/{id}', [CategoriesApiController::class, 'show']);
Route::post('/categories', [CategoriesApiController::class, 'store']);
Route::put('/categories/{id}', [CategoriesApiController::class, 'update']);
Route::delete('/categories/{id}', [CategoriesApiController::class, 'destroy']);
Route::put('/categories/order', [CategoriesApiController::class, 'reorder']);