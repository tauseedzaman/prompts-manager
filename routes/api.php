<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    // User info
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Prompts
    Route::apiResource('prompts', \App\Http\Controllers\Api\PromptController::class);
    
    // Categories
    Route::apiResource('categories', \App\Http\Controllers\Api\CategoryController::class);
    
    // Collections
    Route::apiResource('collections', \App\Http\Controllers\Api\CollectionController::class);
    
    // Tags
    Route::apiResource('tags', \App\Http\Controllers\Api\TagController::class);
});
