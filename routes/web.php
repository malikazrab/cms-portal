<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SettingController;

// ======================= Admin Routes ========================


Route::middleware(['auth', 'admin.auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');


    // ======================= Post Controller Routes ========================

    Route::resource('posts', PostController::class);

    // ======================= Page Controller Routes ========================

    Route::resource('pages', PageController::class);

    // ======================= Media Controller Routes ========================

    Route::resource('media', MediaController::class);
    
    // ======================= Category & Setting Controller Routes ========================

    Route::resource('categories', CategoryController::class)->except(['show', 'edit', 'create']);
    Route::resource('settings', SettingController::class)->only(['index', 'update']);

    });

Route::get('/', function () {
    return view('welcome');
});
Route::middleware(['auth', 'admin.auth'])->prefix('admin')->name('admin.')->group(function () {
    
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
