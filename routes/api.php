<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LocationController; // PASTIKAN INI DIIMPOR (Api kecil)
// use App\Http\Controllers\SmartCaneDataController; // Bisa dikomentari/dihapus jika tidak dipakai lagi untuk peta

/* ... (middleware auth:sanctum) ... */

// === ROUTE UNTUK MENERIMA DATA LOKASI DARI SMART CANE (ESP32) ===
Route::post('/update-location', [LocationController::class, 'updateLocation']); // Ini sudah benar

// === ROUTE UNTUK MENGAMBIL DATA LOKASI UNTUK PETA LEAFLET ===
Route::get('/get-latest-location/{deviceId}', [LocationController::class, 'getLatestLocation']); // PASTIKAN INI AKTIF DAN BENAR

// Rute yang menggunakan SmartCaneDataController dan bernama 'api.smartcane.latest' bisa dikomentari atau dihapus
// jika Anda tidak lagi menggunakannya untuk peta ini:
// Route::get('/smartcane-latest-data', [SmartCaneDataController::class, 'getLatestSmartCaneData'])
//      ->name('api.smartcane.latest');
?>