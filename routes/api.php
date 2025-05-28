<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LocationController;   // Pastikan path ini benar jika LocationController ada di App\Http\Controllers\Api
use App\Http\Controllers\ImageDetectionController; // Pastikan ImageDetectionController ada di App\Http\Controllers


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// === ROUTE UNTUK DATA LOKASI DARI SMART CANE (ESP32) ===
// Pastikan controller LocationController ada dan namespace-nya benar
// Jika tidak ada atau belum digunakan, Anda bisa mengomentari blok ini
if (class_exists(LocationController::class)) {
    Route::post('/update-location', [LocationController::class, 'updateLocation']);
    Route::get('/get-latest-location/{deviceId}', [LocationController::class, 'getLatestLocation']);
}


// === ROUTE UNTUK MENGAMBIL INFO DETEKSI GAMBAR TERBARU DARI DATABASE ===
// Ini adalah route yang akan dipanggil oleh JavaScript di dashboard untuk polling data gambar.
// Pastikan ImageDetectionController ada dan method getLatestDetectionInfoFromDb juga ada di dalamnya.
if (class_exists(ImageDetectionController::class)) {
    Route::get('/get-latest-detection-from-db', [ImageDetectionController::class, 'getLatestDetectionInfoFromDb']);
}

?>