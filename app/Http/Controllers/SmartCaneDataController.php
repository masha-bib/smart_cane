<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon; // Untuk format tanggal

class SmartCaneDataController extends Controller
{
    public function getLatestSmartCaneData()
    {
        try {
            // Ambil record terbaru dari tabel 'detections'
            // Koneksi default akan digunakan (yang sudah diset ke SQLite)
            $latestDbRecord = DB::table('detections')
                                ->orderBy('timestamp', 'desc') // Pastikan kolom timestamp ada dan diisi dengan benar
                                ->first();

            if (!$latestDbRecord) {
                return response()->json([
                    'message'     => 'Belum ada data dari SmartCane.',
                    'latitude'    => null,
                    'longitude'   => null,
                    'accuracy'    => null,
                    'objects'     => [],
                    'voice_alert' => null,
                    'timestamp'   => Carbon::now()->toIso8601String(),
                    'image_url'   => null
                ], 200); // HTTP 200 OK
            }

            $objectsArray = json_decode($latestDbRecord->detected_objects_json, true); // true untuk array asosiatif

            // Logika untuk image_url (jika Anda menyimpan dan menyajikan gambar dari Python/Laravel)
            $imageUrl = null;
            // if ($latestDbRecord->image_path) {
            //     // Contoh jika gambar ada di public/storage/captures (setelah php artisan storage:link)
            //     // $imageUrl = asset('storage/captures/' . basename($latestDbRecord->image_path));
            //     // Atau jika Python server menyajikan gambar:
            //     // $imageUrl = "http://localhost:PYTHON_PORT/images/" . basename($latestDbRecord->image_path);
            // }


            $responseData = [
                'latitude'    => (float) $latestDbRecord->latitude,
                'longitude'   => (float) $latestDbRecord->longitude,
                'accuracy'    => (float) $latestDbRecord->accuracy,
                'objects'     => $objectsArray ?: [], // Pastikan array jika null atau gagal decode
                'voice_alert' => $latestDbRecord->voice_alert_text,
                'timestamp'   => Carbon::parse($latestDbRecord->timestamp)->toIso8601String(), // Konversi ke ISO 8601
                'image_url'   => $imageUrl
            ];

            return response()->json($responseData);

        } catch (\Exception $e) {
            \Log::error("Error fetching SmartCane data: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine());
            return response()->json(['error' => 'Gagal mengambil data SmartCane.', 'details' => $e->getMessage()], 500);
        }
    }
}