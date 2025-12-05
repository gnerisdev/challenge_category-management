<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoriesApiController;

Route::get('/categories/statistics', [CategoriesApiController::class, 'statistics']);
Route::get('/categories', [CategoriesApiController::class, 'show']);
Route::post('/categories', [CategoriesApiController::class, 'store']);
Route::put('/categories/reorder', [CategoriesApiController::class, 'reorder']);
Route::get('/categories/trashed/list', [CategoriesApiController::class, 'trashed']);
Route::get('/categories/{id}', [CategoriesApiController::class, 'show']);
Route::put('/categories/{id}', [CategoriesApiController::class, 'update']);
Route::delete('/categories/{id}', [CategoriesApiController::class, 'destroy']);
Route::post('/categories/trashed/{id}/restore', [CategoriesApiController::class, 'restore']);
Route::delete('/categories/trashed/{id}/permanent', [CategoriesApiController::class, 'deletePermanent']);