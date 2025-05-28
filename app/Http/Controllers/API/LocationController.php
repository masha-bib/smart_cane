<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location; // Model untuk data lokasi
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LocationController extends Controller
{
    /**
     * Menyimpan data lokasi baru dari perangkat.
     */
    public function updateLocation(Request $request)
    {
        // Validasi input dari ESP32
        $validatedData = $request->validate([
            'device_id' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90', // Validasi rentang latitude
            'longitude' => 'required|numeric|between:-180,180', // Validasi rentang longitude
            'satellites' => 'sometimes|nullable|integer|min:0',
            'hdop' => 'sometimes|nullable|numeric|min:0',
            // 'gps_timestamp' => 'sometimes|nullable|date_format:Y-m-d H:i:s' // Jika Anda memutuskan mengirim timestamp dari GPS
        ]);

        try {
            // Menggunakan Eloquent Model untuk membuat record baru
            $location = Location::create([
                'device_id' => $validatedData['device_id'],
                'latitude' => $validatedData['latitude'],
                'longitude' => $validatedData['longitude'],
                'satellites' => $validatedData['satellites'] ?? null, // Gunakan null jika tidak ada
                'hdop' => $validatedData['hdop'] ?? null,             // Gunakan null jika tidak ada
                // 'recorded_at_gps' => $validatedData['gps_timestamp'] ?? null, // Jika ada
            ]);

            Log::info('LocationAPI: Location data stored successfully for device: ' . $validatedData['device_id'], ['data' => $location->toArray()]);
            return response()->json(['message' => 'Location data received and stored successfully.', 'data' => $location], 201); // 201 Created

        } catch (\Illuminate\Database\QueryException $qe) {
            Log::error('LocationAPI: Database query error while storing location data. Error: ' . $qe->getMessage(), ['sql' => $qe->getSql(), 'bindings' => $qe->getBindings()]);
            return response()->json(['error' => 'Failed to store location data due to a database issue.', 'details' => $qe->getMessage()], 500);
        } catch (\Exception $e) {
            Log::error('LocationAPI: General error while storing location data. Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to store location data.', 'details' => $e->getMessage()], 500);
        }
    }


    /**
     * Mengambil data lokasi terbaru UNTUK DEVICE TERTENTU untuk ditampilkan di Leaflet,
     * DAN MENGAMBIL GAMBAR DETEKSI TERBARU DARI TABEL 'deteksi_objek'.
     */
    public function getLatestLocation($deviceId)
    {
        Log::info("LocationAPI: Fetching latest location for device_id: {$deviceId}");
        try {
            // 1. Ambil data lokasi terbaru untuk device_id dari tabel 'locations'
            $location = Location::where('device_id', $deviceId)
                                ->orderBy('created_at', 'desc') // Asumsi 'created_at' adalah timestamp saat record lokasi dibuat
                                ->first();

            // Variabel untuk menyimpan informasi gambar dari Python
            $pythonDetectedImageUrl = null;
            $pythonDetectedObject = null;
            $pythonDetectedTimestamp = null;

            // 2. Ambil gambar deteksi terbaru dari tabel 'deteksi_objek' (database smart_cane)
            try {
                // ---- PERUBAHAN DI SINI ----
                $latestDetectionFromPython = DB::table('deteksi_objek') // Menggunakan tabel 'deteksi_objek'
                                            ->orderBy('waktu', 'desc')    // Order by 'waktu' dari tabel deteksi_objek
                                            ->first();

                if ($latestDetectionFromPython) {
                    // Pastikan kolom 'nama_file' dan 'kategori' ada
                    if (!empty($latestDetectionFromPython->nama_file) && isset($latestDetectionFromPython->kategori)) {
                        // Membuat URL menggunakan route yang sudah kita definisikan di web.php
                        $pythonDetectedImageUrl = route('serve.detected.image', ['filename' => $latestDetectionFromPython->nama_file]);
                        $pythonDetectedObject = $latestDetectionFromPython->kategori; // Menggunakan kolom 'kategori'
                        $pythonDetectedTimestamp = $latestDetectionFromPython->waktu ? Carbon::parse($latestDetectionFromPython->waktu)->isoFormat('D MMMM YYYY, HH:mm:ss') : null;

                        Log::info("LocationAPI: Found latest Python detection from 'deteksi_objek': Filename - {$latestDetectionFromPython->nama_file}, Object - {$pythonDetectedObject}");
                    } else {
                        Log::warning("LocationAPI: Latest detection found in 'deteksi_objek', but 'nama_file' or 'kategori' is missing/empty.", (array) $latestDetectionFromPython);
                    }
                } else {
                    Log::info("LocationAPI: No recent Python detection found in 'deteksi_objek'.");
                }
                // ---- AKHIR PERUBAHAN ----
            } catch (\Exception $e_db_python_img) {
                Log::error("LocationAPI: Error fetching latest Python detection from 'deteksi_objek': " . $e_db_python_img->getMessage());
                // Tidak menghentikan proses, biarkan $pythonDetectedImageUrl tetap null jika error
            }


            if ($location) { // Jika data lokasi ditemukan
                $responseData = [
                    'latitude'    => (float) $location->latitude,
                    'longitude'   => (float) $location->longitude,
                    // Akurasi bisa diambil dari HDOP jika ada, atau default
                    'accuracy'    => $location->hdop ? round((float) $location->hdop * 5, 0) : 50, // Contoh sederhana
                    'timestamp'   => $location->created_at->toIso8601String(), // Waktu data lokasi ini disimpan
                    'device_id'   => $location->device_id,

                    // Data dari deteksi_objek (Python)
                    'image_url'   => $pythonDetectedImageUrl, // Ini adalah URL gambar terbaru dari Python
                    'objects'     => $pythonDetectedObject ? [$pythonDetectedObject] : [], // Array objek terdeteksi
                    'detection_timestamp' => $pythonDetectedTimestamp, // Timestamp kapan objek ini dideteksi oleh Python

                    // Data lain dari tabel 'locations' jika ada dan relevan
                    'hdop'        => $location->hdop ? (float) $location->hdop : null,
                    'satellites'  => $location->satellites ? (int) $location->satellites : null,
                    // 'voice_alert' => $location->voice_alert, // Hapus jika tidak ada di tabel 'locations'
                    // 'event_detected' => ..., // Hapus jika tidak ada di tabel 'locations'
                ];
                Log::info("LocationAPI: Returning combined location and latest detection data for device {$deviceId}.");
                return response()->json($responseData);
            } else { // Jika tidak ada data lokasi untuk device tersebut
                Log::warning("LocationAPI: No location data found for device_id: {$deviceId}. Returning latest Python detection if available.");
                // Tetap kirim data deteksi Python terbaru jika ada, meskipun lokasi device belum ada
                // Ini memungkinkan dashboard menampilkan setidaknya gambar terbaru.
                return response()->json([
                    'message'     => 'Belum ada data lokasi dari SmartCane untuk device ini.',
                    'latitude'    => null,
                    'longitude'   => null,
                    'accuracy'    => null,
                    'timestamp'   => now()->toIso8601String(), // Waktu saat ini karena tidak ada timestamp lokasi
                    'device_id'   => $deviceId,
                    'image_url'   => $pythonDetectedImageUrl,
                    'objects'     => $pythonDetectedObject ? [$pythonDetectedObject] : [],
                    'detection_timestamp' => $pythonDetectedTimestamp,
                    'hdop'        => null,
                    'satellites'  => null,
                ], 200); // Mengembalikan 200 OK
            }
        } catch (\Exception $e) {
            Log::error("LocationAPI: General error in getLatestLocation for {$deviceId}: ".$e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
            return response()->json(['error' => 'Gagal mengambil data lokasi.', 'details' => 'Server error, please check logs.'], 500);
        }
    }

    // Jika ada method index, store, _captureAndSaveImage, pastikan juga sudah sesuai
    // atau hapus jika tidak digunakan.
}