<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon; // Pastikan Carbon di-import

class ImageDetectionController extends Controller
{
    // Method storeFromPython() bisa Anda hapus jika Python sudah langsung menulis ke database
    // dan tidak lagi memanggil endpoint Laravel untuk menyimpan data deteksi awal.
    /*
    public function storeFromPython(Request $request)
    {
        // Logika lama untuk menerima data dari Python dan menyimpannya ke DB.
        // Jika Python sekarang langsung insert ke DB, method ini mungkin tidak diperlukan lagi.
        // Contoh logika lama (jika masih relevan):
        // $request->validate([
        //     'filename' => 'required|string',
        //     'category' => 'required|string',
        //     // tambahkan validasi lain jika perlu
        // ]);
        //
        // try {
        //     DB::table('deteksi_objek')->insert([
        //         'nama_file' => $request->input('filename'),
        //         'kategori' => $request->input('category'),
        //         'waktu' => Carbon::now()
        //     ]);
        //     Log::info('Data deteksi dari Python berhasil disimpan via API Laravel.');
        //     return response()->json(['message' => 'Detection data stored successfully by Laravel'], 201);
        // } catch (\Exception $e) {
        //     Log::error('Gagal menyimpan data deteksi dari Python via API Laravel: ' . $e->getMessage());
        //     return response()->json(['error' => 'Failed to store detection data by Laravel'], 500);
        // }
    }
    */

    public function getLatestDetectionInfoFromDb()
    {
        Log::info('API Polling: Menerima permintaan ke getLatestDetectionInfoFromDb');
        try {
            // Query ke database default Laravel (yang sudah dikonfigurasi ke 'smart_cane' di .env)
            $latestDetection = DB::table('deteksi_objek')
                                ->orderBy('waktu', 'desc') // Order berdasarkan waktu terbaru
                                ->first();

            if ($latestDetection) {
                // Pastikan kolom yang dibutuhkan ada dan tidak kosong
                if (!empty($latestDetection->nama_file) && isset($latestDetection->kategori)) {
                    $responseData = [
                        'data' => [
                            // Menggunakan route helper untuk membuat URL gambar yang aman
                            'url' => route('serve.detected.image', ['filename' => $latestDetection->nama_file]),
                            'filename' => $latestDetection->nama_file, // Nama file mentah
                            'timestamp' => $latestDetection->waktu ? Carbon::parse($latestDetection->waktu)->isoFormat('D MMMM YYYY, HH:mm:ss') : 'N/A',
                            'detected_object' => $latestDetection->kategori, // Menggunakan kolom 'kategori'
                        ]
                    ];
                    Log::info('API Polling: Mengembalikan data gambar terbaru dari DB:', $responseData);
                    return response()->json($responseData);
                } else {
                    // Jika data ditemukan tapi kolom penting kosong
                    Log::warning('API Polling: Data deteksi terbaru ditemukan di DB, tetapi kolom nama_file atau kategori kosong/tidak ada.', (array) $latestDetection);
                    return response()->json(['message' => 'Recent detection found but critical data (filename/category) is missing.'], 200); // Tetap 200 OK, tapi dengan pesan
                }
            }

            // Jika tidak ada data sama sekali di tabel
            Log::info('API Polling: Tidak ada data deteksi terbaru ditemukan di tabel deteksi_objek.');
            return response()->json(['message' => 'No recent detection data found in the database.'], 404);

        } catch (\Exception $e) {
            Log::error("API Polling Error - Gagal mengambil data dari DB. Pesan Exception: " . $e->getMessage() . " - File: " . $e->getFile() . " Baris: " . $e->getLine());
            return response()->json(['error' => 'Failed to fetch latest detection data due to a server error.'], 500);
        }
    }
}