<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('verified')->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'updateInfo'])->name('profile.info');
    Route::put('/profile', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});

Route::get('/clients', [ClientController::class, 'index'])->name('clients');
Route::post('/clients', [ClientController::class, 'store'])->name('clients');
Route::put('/clients', [ClientController::class, 'update'])->name('clients');

Route::inertia('/', 'Home')->name('home');
Route::inertia('/about', 'About')->name('about');
require __DIR__ . '/auth.php';