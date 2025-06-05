<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LocationController;   
use App\Http\Controllers\ImageDetectionController; 

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

if (class_exists(LocationController::class)) {
    Route::post('/update-location', [LocationController::class, 'updateLocation']);
    Route::get('/get-latest-location/{deviceId}', [LocationController::class, 'getLatestLocation']);
}

if (class_exists(ImageDetectionController::class)) {
    Route::get('/get-latest-detection-from-db', [ImageDetectionController::class, 'getLatestDetectionInfoFromDb']);
}

?>