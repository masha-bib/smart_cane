<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RekamanObjek;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SmartCaneChartController extends Controller
{
    private function getKategoriColor(string $kategori): string
    {
        // ... (fungsi getKategoriColor tetap sama) ...
        $kategoriLower = strtolower($kategori);
        $colors = [
            'orang'           => 'rgba(255, 99, 132, 0.8)',
            'kendaraan'       => 'rgba(59, 130, 246, 0.8)',
            'pohon'           => 'rgba(34, 197, 94, 0.8)',
            'bangunan'        => 'rgba(168, 85, 247, 0.8)',
            'lubang'          => 'rgba(234, 179, 8, 0.8)',
            'tangga'          => 'rgba(249, 115, 22, 0.8)',
            'trotoar'         => 'rgba(99, 102, 241, 0.8)',
            'lainnya'         => 'rgba(107, 114, 128, 0.8)'
        ];
        return $colors[$kategoriLower] ?? $colors['lainnya'];
    }

    public function index(Request $request)
    {
        $kategoriDikecualikan = ['tidak terdeteksi', 'tidak dikenal'];
        $periode = $request->input('periode', 'semua');

        $query = RekamanObjek::select('kategori', DB::raw('count(*) as total'))
                            ->whereNotIn(DB::raw('LOWER(kategori)'), $kategoriDikecualikan);

        switch ($periode) {
            case 'hari_ini':
                $query->whereDate('waktu', Carbon::today());
                break;
            case '7_hari':
                $query->where('waktu', '>=', Carbon::now()->subDays(7)->startOfDay());
                $query->where('waktu', '<=', Carbon::now()->endOfDay());
                break;
            case '30_hari':
                $query->where('waktu', '>=', Carbon::now()->subDays(30)->startOfDay());
                $query->where('waktu', '<=', Carbon::now()->endOfDay());
                break;
            case 'bulan_ini':
                $query->whereMonth('waktu', Carbon::now()->month)
                      ->whereYear('waktu', Carbon::now()->year);
                break;
            case 'bulan_lalu':
                $query->whereMonth('waktu', Carbon::now()->subMonthNoOverflow()->month)
                      ->whereYear('waktu', Carbon::now()->subMonthNoOverflow()->year);
                break;
        }

        $jumlahPerKategori = $query->groupBy('kategori')
                                   ->orderBy('total', 'desc')
                                   ->get();

        $labels = [];
        $data = [];
        $colors = [];
        $totalDeteksi = 0;
        $dataKosong = false; // <--- Inisialisasi flag dataKosong

        if ($jumlahPerKategori->isEmpty()) { // <--- Cek jika koleksi hasil query kosong
            $dataKosong = true;
        } else {
            foreach ($jumlahPerKategori as $item) {
                $labels[] = ucfirst($item->kategori);
                $currentData = $item->total;
                $data[] = $currentData;
                $totalDeteksi += $currentData;
                $colors[] = $this->getKategoriColor($item->kategori);
            }
        }

        $dataUntukChart = [
            'labels' => $labels,
            'data'   => $data,
            'colors' => $colors,
            'totalDeteksi' => $totalDeteksi,
        ];

        // Kirim flag $dataKosong dan periode aktif ke view
        return view('chart_app', [
            'smartCaneDetectionData' => $dataUntukChart,
            'dataKosong' => $dataKosong, // <--- Kirim flag ini
            'periodeAktif' => $periode // Untuk menampilkan nama periode di pesan jika data kosong
        ]);
    }
}