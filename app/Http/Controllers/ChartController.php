<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RekamanObjek; 
use Illuminate\Support\Facades\DB;

class ChartController extends Controller
{
    private function getKategoriColor(string $kategori): string
    {
        $colors = [
            'orang'    => 'rgba(255, 99, 132, 0.8)',  
            'pohon'    => 'rgba(75, 192, 192, 0.8)',  
            'mobil'    => 'rgba(54, 162, 235, 0.8)', 
            'lubang'   => 'rgba(255, 206, 86, 0.8)',  
            'bangunan' => 'rgba(153, 102, 255, 0.8)', 
            'tangga'   => 'rgba(255, 159, 64, 0.8)',  
            'lainnya'  => 'rgba(201, 203, 207, 0.8)' 
        ];
        return $colors[strtolower($kategori)] ?? $colors['lainnya'];
    }

    public function tampilkanChartKategori()
    {
        $jumlahPerKategori = RekamanObjek::select('kategori', DB::raw('count(*) as total'))
                                    ->groupBy('kategori')
                                    ->orderBy('total', 'desc') 
                                    ->get();

        
        $labels = []; 
        $data = [];  
        $colors = [];

        if ($jumlahPerKategori->isNotEmpty()) {
            foreach ($jumlahPerKategori as $item) {
                $labels[] = ucfirst($item->kategori); 
                $data[] = $item->total;
                $colors[] = $this->getKategoriColor($item->kategori);
            }
        }

        $dataUntukChart = [
            'labels' => $labels,
            'data'   => $data,
            'colors' => $colors,
        ];

        return view('chart_app', ['smartCaneDetectionData' => $dataUntukChart]);
    }
}