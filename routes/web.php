<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SlugController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\RoleManagementController;
use App\Http\Controllers\MenuController;  // <--- ADD THIS LINE
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin.auth'])
    ->prefix('admin')
    ->name('admin.')
    ->middleware('activity')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('permission:dashboard.view')->name('dashboard');

        Route::get('posts', [PostController::class, 'index'])->middleware('permission:posts.view')->name('posts.index');
        Route::get('posts/create', [PostController::class, 'create'])->middleware('permission:posts.create')->name('posts.create');
        Route::post('posts', [PostController::class, 'store'])->middleware('permission:posts.create')->name('posts.store');
        Route::get('posts/{post}/edit', [PostController::class, 'edit'])->middleware('permission:posts.update')->name('posts.edit');
        Route::put('posts/{post}', [PostController::class, 'update'])->middleware('permission:posts.update')->name('posts.update');
        Route::delete('posts/{post}', [PostController::class, 'destroy'])->middleware('permission:posts.delete')->name('posts.destroy');

        Route::get('pages', [PageController::class, 'index'])->middleware('permission:pages.view')->name('pages.index');
        Route::get('pages/create', [PageController::class, 'create'])->middleware('permission:pages.create')->name('pages.create');
        Route::post('pages', [PageController::class, 'store'])->middleware('permission:pages.create')->name('pages.store');
        Route::get('pages/{page}/edit', [PageController::class, 'edit'])->middleware('permission:pages.update')->name('pages.edit');
        Route::put('pages/{page}', [PageController::class, 'update'])->middleware('permission:pages.update')->name('pages.update');
        Route::delete('pages/{page}', [PageController::class, 'destroy'])->middleware('permission:pages.delete')->name('pages.destroy');

        Route::get('media', [MediaController::class, 'index'])->middleware('permission:media.view')->name('media.index');
        Route::delete('media/{medium}', [MediaController::class, 'destroy'])->middleware('permission:media.delete')->name('media.destroy');
        Route::post('/media/upload', [MediaController::class, 'upload'])->middleware('permission:media.upload')->name('media.upload');

        Route::get('categories', [CategoryController::class, 'index'])->middleware('permission:categories.view')->name('categories.index');
        Route::post('categories', [CategoryController::class, 'store'])->middleware('permission:categories.create')->name('categories.store');
        Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->middleware('permission:categories.create')->name('categories.destroy');

        Route::get('/settings', [SettingController::class, 'index'])->middleware('permission:settings.manage')->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->middleware('permission:settings.manage')->name('settings.update');
        Route::get('/settings/sections/{section}', [SettingController::class, 'editSection'])->middleware('permission:settings.manage')->name('settings.sections.edit');

        Route::get('users', [UserManagementController::class, 'index'])->middleware('permission:users.manage')->name('users.index');
        Route::get('users/create', [UserManagementController::class, 'create'])->middleware('permission:users.manage')->name('users.create');
        Route::post('users', [UserManagementController::class, 'store'])->middleware('permission:users.manage')->name('users.store');
        Route::get('users/{user}/edit', [UserManagementController::class, 'edit'])->middleware('permission:users.manage')->name('users.edit');
        Route::put('users/{user}', [UserManagementController::class, 'update'])->middleware('permission:users.manage')->name('users.update');
        Route::delete('users/{user}', [UserManagementController::class, 'destroy'])->middleware('permission:users.manage')->name('users.destroy');
        Route::patch('users/{user}/role', [UserManagementController::class, 'updateRole'])->middleware('permission:users.manage')->name('users.role.update');

        Route::get('roles', [RoleManagementController::class, 'index'])->middleware('permission:users.manage')->name('roles.index');
        Route::get('roles/{role}', [RoleManagementController::class, 'show'])->middleware('permission:users.manage')->name('roles.show');

        Route::get('activity-logs', [ActivityLogController::class, 'index'])->middleware('permission:activity.view')->name('activity-logs.index');

        Route::match(['get', 'post'], '/slug', [SlugController::class, 'generate'])->middleware('permission:pages.create')->name('slug.generate');

        // ============================================================
        // ========== MENU ROUTES (PHASE 2 - ADDED CORRECTLY) ==========
        // ============================================================
        Route::get('/menus', [MenuController::class, 'index'])->name('menus.index');
        Route::get('/menus/create', [MenuController::class, 'create'])->name('menus.create');
        Route::post('/menus', [MenuController::class, 'store'])->name('menus.store');
        Route::get('/menus/{menu}/edit', [MenuController::class, 'edit'])->name('menus.edit');
        Route::put('/menus/{menu}', [MenuController::class, 'update'])->name('menus.update');
        Route::delete('/menus/{menu}', [MenuController::class, 'destroy'])->name('menus.destroy');
        Route::post('/menus/{menu}/set-default', [MenuController::class, 'setDefault'])->name('menus.set-default');
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

Route::middleware(['auth', 'activity'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/admin/pages/create', [PageController::class, 'create'])->name('admin.pages.create');
Route::post('/admin/pages', [PageController::class, 'store'])->name('admin.pages.store');
Route::put('/admin/pages/{page}', [PageController::class, 'update'])->name('admin.pages.update'); 

require __DIR__.'/auth.php';