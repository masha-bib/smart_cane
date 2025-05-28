<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RekamanObjek; // Pastikan ini nama model Anda
use Carbon\Carbon;

class DeteksiObjekSeeder extends Seeder
{
    public function run(): void
    {
        // RekamanObjek::truncate(); // Hati-hati jika sudah ada data penting, hapus baris ini jika tidak ingin mengosongkan tabel

        $kategoriList = ['orang', 'pohon', 'mobil', 'lubang', 'bangunan', 'tangga', 'trotoar', 'kendaraan lain'];
        $entries = [];

        if (RekamanObjek::count() == 0) { // Hanya jalankan jika tabel kosong
            for ($i = 0; $i < 75; $i++) { // Buat 75 data contoh
                $kategori = $kategoriList[array_rand($kategoriList)];
                $entries[] = [
                    'nama_file' => 'file_contoh_' . uniqid() . '.jpg',
                    'kategori'  => $kategori,
                    'waktu'     => Carbon::now()->subDays(rand(0, 60))->subHours(rand(0,23))->subMinutes(rand(0,59)),
                ];
            }
            RekamanObjek::insert($entries);
            $this->command->info('Tabel deteksi_objek telah diisi dengan 75 data contoh.');
        } else {
            $this->command->info('Tabel deteksi_objek sudah berisi data, seeder tidak dijalankan untuk menambah data baru.');
        }
    }
}