<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

// Halaman publik
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : view('welcome');
});

// Semua route di bawah memerlukan login (middleware 'auth')
Route::middleware('auth')->group(function () {

    // Dashboard — menampilkan konten berbeda berdasarkan role
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // =========================================================
    //  TICKET ROUTES
    //  Route::resource() otomatis membuat 7 route standar:
    //    GET    /tickets              → index
    //    GET    /tickets/create       → create
    //    POST   /tickets              → store
    //    GET    /tickets/{ticket}     → show
    //    GET    /tickets/{ticket}/edit → edit
    //    PUT    /tickets/{ticket}     → update
    //    DELETE /tickets/{ticket}     → destroy
    // =========================================================
    Route::resource('tickets', TicketController::class)
        ->only(['index', 'create', 'store', 'show', 'update']);

    // Route tambahan: POST komentar ke tiket
    // Nested di bawah tiket: /tickets/{ticket}/comments
    Route::post('/tickets/{ticket}/comments', [TicketController::class, 'storeComment'])
        ->name('tickets.comments.store');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
