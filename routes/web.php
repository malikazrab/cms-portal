<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SlugController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin.auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('posts', PostController::class)->except(['show']);
        Route::resource('pages', PageController::class)->except(['show']);

        Route::resource('media', MediaController::class)->only(['index', 'destroy']);
        Route::post('/media/upload', [MediaController::class, 'upload'])->name('media.upload');

        Route::resource('categories', CategoryController::class)->only(['index', 'store', 'destroy']);

        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
        Route::get('/settings/sections/{section}', [SettingController::class, 'editSection'])->name('settings.sections.edit');

        Route::match(['get', 'post'], '/slug', [SlugController::class, 'generate'])->name('slug.generate');
    });

Route::middleware('throttle:60,1')->group(function () {
    Route::get('/', [PublicController::class, 'home'])->name('public.home');
    Route::get('/blog', [PublicController::class, 'blog'])->name('public.blog');
    Route::get('/media/{path}', [PublicController::class, 'serveMedia'])->where('path', '.*')->name('public.media');
    Route::get('/blog/{slug}', [PublicController::class, 'showPost'])->name('public.post');
    Route::get('/pages/{slug}', [PublicController::class, 'showPage'])->name('public.page');
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
