<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Http\Controllers\MarketplaceController::class, 'welcome'])->name('home');

Route::get('/marketplace', [\App\Http\Controllers\MarketplaceController::class, 'index'])->name('marketplace.index');
Route::get('/marketplace/{prompt}', [\App\Http\Controllers\MarketplaceController::class, 'show'])->name('marketplace.show');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('prompts.index');
    })->name('dashboard');

    Route::get('/prompts/favorites', [\App\Http\Controllers\PromptController::class, 'favorites'])->name('prompts.favorites');
    Route::get('/prompts/export', [\App\Http\Controllers\PromptController::class, 'export'])->name('prompts.export');
    Route::get('/prompts/import', [\App\Http\Controllers\PromptController::class, 'importPage'])->name('prompts.import-page');
    Route::post('/prompts/import', [\App\Http\Controllers\PromptController::class, 'import'])->name('prompts.import');
    Route::get('/prompts/sample', [\App\Http\Controllers\PromptController::class, 'downloadSample'])->name('prompts.sample');
    
    // Auth-only Marketplace Routes
    Route::post('/marketplace/{prompt}/rate', [\App\Http\Controllers\MarketplaceController::class, 'rate'])->name('marketplace.rate');
    Route::post('/prompts/{prompt}/fork', [\App\Http\Controllers\PromptController::class, 'fork'])->name('prompts.fork');

    Route::resource('prompts', \App\Http\Controllers\PromptController::class);
    Route::resource('categories', \App\Http\Controllers\CategoryController::class);
    Route::resource('tags', \App\Http\Controllers\TagController::class);
    Route::post('/prompts/{prompt}/copy', [\App\Http\Controllers\PromptController::class, 'copy'])->name('prompts.copy');
    Route::post('/prompts/{prompt}/toggle-favorite', [\App\Http\Controllers\PromptController::class, 'toggleFavorite'])->name('prompts.toggle-favorite');
    Route::get('/prompts/{prompt}/history', [\App\Http\Controllers\PromptController::class, 'history'])->name('prompts.history');
    Route::post('/versions/{version}/copy', [\App\Http\Controllers\PromptController::class, 'copyVersion'])->name('versions.copy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // API Token Management
    Route::get('/profile/api-tokens', [\App\Http\Controllers\ApiTokenController::class, 'index'])->name('api-tokens.index');
    Route::post('/profile/api-tokens', [\App\Http\Controllers\ApiTokenController::class, 'store'])->name('api-tokens.store');
    Route::delete('/profile/api-tokens/{tokenId}', [\App\Http\Controllers\ApiTokenController::class, 'destroy'])->name('api-tokens.destroy');
});

require __DIR__.'/auth.php';
