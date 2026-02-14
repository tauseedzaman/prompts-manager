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

    Route::resource('prompts', \App\Http\Controllers\PromptController::class);
    Route::resource('categories', \App\Http\Controllers\CategoryController::class);
    Route::resource('collections', \App\Http\Controllers\CollectionController::class);
    Route::resource('tags', \App\Http\Controllers\TagController::class);
    Route::post('/prompts/{prompt}/copy', [\App\Http\Controllers\PromptController::class, 'copy'])->name('prompts.copy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
