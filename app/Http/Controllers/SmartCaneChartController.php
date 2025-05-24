<?php

namespace App\Http\Controllers; // Pastikan namespace benar

use Illuminate\Http\Request;

class SmartCaneChartController extends Controller // Pastikan nama class benar
{
    public function index() // Atau nama method yang Anda gunakan
    {
        // Data deteksi (contoh statis, nanti bisa dari database)
        $smartCaneDetectionData = [ // Ganti nama variabel jika perlu agar konsisten
            'labels' => ['Lubang', 'Orang', 'Kendaraan', 'Pohon', 'Tangga', 'Lainnya'],
            'data' => [35, 25, 15, 10, 5, 10],
            'colors' => [
                'rgba(255, 99, 132, 0.8)',
                'rgba(54, 162, 235, 0.8)',
                'rgba(255, 206, 86, 0.8)',
                'rgba(75, 192, 192, 0.8)',
                'rgba(153, 102, 255, 0.8)',
                'rgba(201, 203, 207, 0.8)'
            ]
        ];

        // Jika Anda ingin view ini menjadi 'chart_app.blade.php'
        // dan Anda ingin data ini tersedia di sana:
        return view('chart_app', compact('smartCaneDetectionData' /*, data lain jika ada */));

        // Atau jika Anda memiliki view khusus untuk SmartCane (misal 'smartcane_chart_view.blade.php'):
        // return view('smartcane_chart_view', compact('smartCaneDetectionData'));
    }

    // Anda bisa menambahkan method lain di sini jika perlu
}