<?php

use App\Http\Controllers\Admin\CarController as AdminCarController;
use App\Http\Controllers\Admin\CatalogController;
use App\Http\Controllers\Admin\RentalController as AdminRentalController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RentalController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/cars', [CarController::class, 'index'])->name('cars.index');
Route::get('/cars/{car}', [CarController::class, 'show'])->name('cars.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');
    Route::get('rentals', [RentalController::class, 'index'])->name('rentals.index');
    Route::post('rentals', [RentalController::class, 'store'])->name('rentals.store');
    Route::patch('rentals/{rental}/cancel', [RentalController::class, 'cancel'])->name('rentals.cancel');
});

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('catalog/{type}', [CatalogController::class, 'index'])->name('catalog.index');
    Route::post('catalog/{type}', [CatalogController::class, 'store'])->name('catalog.store');
    Route::put('catalog/{type}/{item}', [CatalogController::class, 'update'])->name('catalog.update');
    Route::delete('catalog/{type}/{item}', [CatalogController::class, 'destroy'])->name('catalog.destroy');

    Route::resource('cars', AdminCarController::class)->except(['show']);
    Route::get('rentals', [AdminRentalController::class, 'index'])->name('rentals.index');
    Route::patch('rentals/{rental}', [AdminRentalController::class, 'update'])->name('rentals.update');
    Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
    Route::patch('users/{user}', [AdminUserController::class, 'update'])->name('users.update');
});

require __DIR__.'/settings.php';
