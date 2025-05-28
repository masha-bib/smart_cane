<?php

// app/Http/Controllers/ChartController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RekamanObjek; // <-- Pastikan ini sesuai dengan nama Model Anda
use Illuminate\Support\Facades\DB;

class ChartController extends Controller
{
    // Fungsi untuk mendapatkan warna berdasarkan kategori
    private function getKategoriColor(string $kategori): string
    {
        $colors = [
            'orang'    => 'rgba(255, 99, 132, 0.8)',  // Merah muda
            'pohon'    => 'rgba(75, 192, 192, 0.8)',  // Hijau toska
            'mobil'    => 'rgba(54, 162, 235, 0.8)',  // Biru
            'lubang'   => 'rgba(255, 206, 86, 0.8)',  // Kuning
            'bangunan' => 'rgba(153, 102, 255, 0.8)', // Ungu
            'tangga'   => 'rgba(255, 159, 64, 0.8)',  // Oranye
            // Tambahkan kategori lain dan warnanya di sini
            'lainnya'  => 'rgba(201, 203, 207, 0.8)'  // Abu-abu untuk default
        ];
        return $colors[strtolower($kategori)] ?? $colors['lainnya'];
    }

    public function tampilkanChartKategori()
    {
        // 1. Ambil data dari database: hitung jumlah kemunculan setiap 'kategori'
        $jumlahPerKategori = RekamanObjek::select('kategori', DB::raw('count(*) as total'))
                                    ->groupBy('kategori')
                                    ->orderBy('total', 'desc') // Urutkan dari yang paling banyak
                                    ->get();

        // 2. Siapkan array untuk data chart
        $labels = []; // Untuk nama-nama kategori (mis: "Orang", "Pohon")
        $data = [];   // Untuk jumlah data per kategori (mis: 10, 5)
        $colors = []; // Untuk warna setiap segmen chart

        // 3. Proses hasil query menjadi format yang dibutuhkan chart
        if ($jumlahPerKategori->isNotEmpty()) {
            foreach ($jumlahPerKategori as $item) {
                $labels[] = ucfirst($item->kategori); // Kapitalisasi huruf pertama kategori
                $data[] = $item->total;
                $colors[] = $this->getKategoriColor($item->kategori);
            }
        }

        // 4. Gabungkan data untuk dikirim ke view
        $dataUntukChart = [
            'labels' => $labels,
            'data'   => $data,
            'colors' => $colors,
        ];

        // 5. Tampilkan view dan kirim data chart
        // Pastikan nama view 'chart_app' sudah benar dan ada di resources/views/
        return view('chart_app', ['smartCaneDetectionData' => $dataUntukChart]);
    }
}