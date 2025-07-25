<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PageDashboardController extends Controller
{
    public function index()
    {
        $latestDetectedImageInfo = null; 
        try {
            $latestDetection = DB::table('deteksi_objek')
                                ->orderBy('waktu', 'desc') 
                                ->first();

            if ($latestDetection) {
                if (!empty($latestDetection->nama_file) && isset($latestDetection->kategori)) {
                    $latestDetectedImageInfo = [
                        'filename' => $latestDetection->nama_file,
                        'timestamp_iso' => $latestDetection->waktu, 
                        'timestamp_formatted' => $latestDetection->waktu ? Carbon::parse($latestDetection->waktu)->isoFormat('D MMMM YYYY, HH:mm:ss') : 'N/A',
                        'detected_object' => $latestDetection->kategori,
                    ];
                    Log::info('PageDashboard: Data deteksi terbaru berhasil diambil dari DB:', $latestDetectedImageInfo);
                } else {
                    Log::warning('PageDashboard: Data deteksi terbaru ditemukan, tetapi kolom nama_file atau kategori kosong/tidak ada.', (array) $latestDetection);
                }
            } else {
                Log::info('PageDashboard: Tidak ada data deteksi ditemukan di tabel deteksi_objek.');
            }
        } catch (\Exception $e) {
            Log::error("PageDashboard Error: Gagal mengambil data dari DB. Pesan: " . $e->getMessage() . " - File: " . $e->getFile() . " Baris: " . $e->getLine());
        }

        return view('dashboard', [
            'latestDetectedImage' => $latestDetectedImageInfo, 
        ]);
    }
}