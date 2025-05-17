// routes/api.php
use App\Http\Controllers\Api\LocationController;

Route::get('/locations', [LocationController::class, 'index']);
Route::post('/locations', [LocationController::class, 'store']); // Jika Anda ingin fungsi tambah dari peta