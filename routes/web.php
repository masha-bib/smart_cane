<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MapController;
use App\Http\Controllers\SmartCaneChartController; // Pastikan ini di atas

Route::get('/', function () {
    return view('welcome');
});

// HAPUS duplikat Route::get('/', ...)

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // MODIFIKASI ROUTE INI
    Route::get('/chart', [SmartCaneChartController::class, 'index']) // Panggil controller di sini
        ->middleware(['auth', 'verified'])
        ->name('chart_app'); // Anda bisa tetap menggunakan nama ini atau menggantinya menjadi 'chart' agar konsisten
});


// Anda bisa MENGHAPUS atau MENGKOMENTARI route di bawah ini jika '/chart' sudah cukup
// Route::get('/tampilan-chart', [SmartCaneChartController::class, 'index'])->name('chart');

require __DIR__.'/auth.php';