<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon; 

class ImageDetectionController extends Controller
{
    public function getLatestDetectionInfoFromDb()
    {
        Log::info('API Polling: Menerima permintaan ke getLatestDetectionInfoFromDb');
        try {
            $latestDetection = DB::table('deteksi_objek')
                                ->orderBy('waktu', 'desc') 
                                ->first();

            if ($latestDetection) {
                if (!empty($latestDetection->nama_file) && isset($latestDetection->kategori)) {
                    $responseData = [
                        'data' => [
                            'url' => route('serve.detected.image', ['filename' => $latestDetection->nama_file]),
                            'filename' => $latestDetection->nama_file, 
                            'timestamp' => $latestDetection->waktu ? Carbon::parse($latestDetection->waktu)->isoFormat('D MMMM YYYY, HH:mm:ss') : 'N/A',
                            'detected_object' => $latestDetection->kategori, 
                        ]
                    ];
                    Log::info('API Polling: Mengembalikan data gambar terbaru dari DB:', $responseData);
                    return response()->json($responseData);
                } else {
                    Log::warning('API Polling: Data deteksi terbaru ditemukan di DB, tetapi kolom nama_file atau kategori kosong/tidak ada.', (array) $latestDetection);
                    return response()->json(['message' => 'Recent detection found but critical data (filename/category) is missing.'], 200); // Tetap 200 OK, tapi dengan pesan
                }
            }

            Log::info('API Polling: Tidak ada data deteksi terbaru ditemukan di tabel deteksi_objek.');
            return response()->json(['message' => 'No recent detection data found in the database.'], 404);

        } catch (\Exception $e) {
            Log::error("API Polling Error - Gagal mengambil data dari DB. Pesan Exception: " . $e->getMessage() . " - File: " . $e->getFile() . " Baris: " . $e->getLine());
            return response()->json(['error' => 'Failed to fetch latest detection data due to a server error.'], 500);
        }
    }
}