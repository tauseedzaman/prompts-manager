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

Route::middleware('auth:sanctum')->name('api.')->group(function () {
    // User info
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Marketplace
    Route::get('/marketplace', [\App\Http\Controllers\Api\MarketplaceController::class, 'index'])->name('marketplace.index');

    // Prompts
    Route::apiResource('prompts', \App\Http\Controllers\Api\PromptController::class);
    
    // Categories
    Route::apiResource('categories', \App\Http\Controllers\Api\CategoryController::class);
    
    // Tags
    Route::apiResource('tags', \App\Http\Controllers\Api\TagController::class);
});
