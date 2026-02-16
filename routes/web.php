<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('prompts.index');
    })->name('dashboard');

    Route::get('/prompts/favorites', [\App\Http\Controllers\PromptController::class, 'favorites'])->name('prompts.favorites');
    Route::resource('prompts', \App\Http\Controllers\PromptController::class);
    Route::resource('categories', \App\Http\Controllers\CategoryController::class);
    Route::resource('collections', \App\Http\Controllers\CollectionController::class);
    Route::resource('tags', \App\Http\Controllers\TagController::class);
    Route::post('/prompts/{prompt}/copy', [\App\Http\Controllers\PromptController::class, 'copy'])->name('prompts.copy');
    Route::post('/prompts/{prompt}/toggle-favorite', [\App\Http\Controllers\PromptController::class, 'toggleFavorite'])->name('prompts.toggle-favorite');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // API Token Management
    Route::get('/profile/api-tokens', [\App\Http\Controllers\ApiTokenController::class, 'index'])->name('api-tokens.index');
    Route::post('/profile/api-tokens', [\App\Http\Controllers\ApiTokenController::class, 'store'])->name('api-tokens.store');
    Route::delete('/profile/api-tokens/{tokenId}', [\App\Http\Controllers\ApiTokenController::class, 'destroy'])->name('api-tokens.destroy');
});

require __DIR__.'/auth.php';
