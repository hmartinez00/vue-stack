<?php

use App\Http\Controllers\MyNewPageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    $message = '¡Hola desde Laravel!'; // Aquí defines el dato
    return Inertia::render('Dashboard', [
        'myMessage' => $message, // Pasas el dato al componente
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

// Esta es la ruta actualizada, que ahora usa un controlador
Route::get('my-new-page', [MyNewPageController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('my-new-page');

Route::resource('products', ProductController::class);
Route::resource('posts', PostController::class);

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
