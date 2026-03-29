<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminPosController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('pos')->name('pos.')->group(function () {
 
    // Main POS screen
    Route::get('/', [AdminPosController::class, 'index'])->name('index');
 
    // Live item search (called via fetch/axios from the POS UI)
    Route::get('/items/search', [AdminPosController::class, 'searchItems'])->name('items.search');
 
    // Checkout — creates invoice + payment + stock movements
    Route::post('/checkout', [AdminPosController::class, 'checkout'])->name('checkout');
 
    // Print / view receipt
    Route::get('/receipt/{invoice}', [AdminPosController::class, 'receipt'])->name('receipt');
 
    // Void an invoice
    Route::patch('/invoice/{invoice}/void', [AdminPosController::class, 'void'])->name('void');
 
    // Today's sales summary strip
    Route::get('/summary', [AdminPosController::class, 'summary'])->name('summary');
 
});
 

require __DIR__.'/auth.php';
