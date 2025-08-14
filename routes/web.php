<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MigrationController;
use App\Http\Controllers\MyNewPageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');


Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('my-new-page', [MyNewPageController::class, 'index'])->name('my-new-page');

    Route::resource('products', ProductController::class);
    Route::resource('posts', PostController::class);
    Route::resource('migrations', MigrationController::class);

});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
