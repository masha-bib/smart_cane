<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SmartCaneChartController;
use App\Http\Controllers\PageDashboardController; 
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [PageDashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/chart', [SmartCaneChartController::class, 'index'])
        ->middleware(['auth', 'verified'])
        ->name('chart_app');
});

Route::get('/detected-images/{filename}', function ($filename) {
    $pythonUploadPath = 'C:/Users/Jakik/Documents/Iot_Olivia/upload'; 

    if (empty($pythonUploadPath) || !is_dir(rtrim($pythonUploadPath, '/'))) {
        Log::error("Konfigurasi Path ke folder upload Python tidak valid atau direktori tidak ditemukan. Path yang dicek: '" . $pythonUploadPath . "'");
        abort(500, 'Image directory server configuration error. Please check server logs.');
    }

    $safeFilename = basename($filename);
    if ($safeFilename !== $filename) {
        Log::warning("Potensi nama file berbahaya terdeteksi dan diblokir: Original: " . $filename . ", Safe: " . $safeFilename);
        abort(400, 'Invalid filename provided.');
    }

    $filePath = rtrim($pythonUploadPath, '/') . '/' . $safeFilename;

    if (!File::exists($filePath)) {
        Log::warning("File gambar tidak ditemukan di server pada path: " . $filePath);
        abort(404, 'Image not found on server.');
    }

    try {
        $fileContent = File::get($filePath); 
        $mimeType = File::mimeType($filePath); 

        if (!$mimeType || !str_starts_with($mimeType, 'image/')) {
            Log::warning("File bukan gambar atau tipe MIME tidak dikenal: " . $filePath . " (Tipe terdeteksi: " . ($mimeType ?: 'Tidak ada') . ")");
            abort(415, 'Unsupported media type. Not a valid image.');
        }

        $response = Response::make($fileContent, 200);
        $response->header("Content-Type", $mimeType);
        return $response;
    } catch (\Exception $e) {
        Log::error("Error saat membaca atau menyajikan file gambar: " . $filePath . " - Pesan Exception: " . $e->getMessage());
        abort(500, 'Error serving image. Please check server logs.');
    }
})->name('serve.detected.image')->where('filename', '[A-Za-z0-9_.-]+');

require __DIR__.'/auth.php';