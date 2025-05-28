<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'device_id',    // Ada di tabel Anda
        'latitude',     // Ada di tabel Anda
        'longitude',    // Ada di tabel Anda
        'satellites',   // Ada di tabel Anda (nullable)
        'hdop',         // Ada di tabel Anda (nullable)
        // 'name',         // TIDAK ADA di skema tabel 'locations' Anda, hapus jika tidak ada
        // 'description',  // TIDAK ADA di skema tabel 'locations' Anda, hapus jika tidak ada
        // 'image_url',    // SUDAH BENAR DIHAPUS DARI TABEL, jadi hapus dari sini juga
        // 'image_path',   // SUDAH BENAR DIHAPUS DARI TABEL, jadi hapus dari sini juga
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // Anda menggunakan decimal(10,7) di DB, casting ke string atau float adalah pilihan.
        // Jika ingin tetap sebagai string untuk presisi:
        'latitude' => 'string',
        'longitude' => 'string',
        // Atau jika float cukup (umumnya lebih mudah diolah di PHP/JS):
        // 'latitude' => 'float',
        // 'longitude' => 'float',

        'hdop' => 'float',       // Atau 'string' jika presisi sangat penting dan Anda handle konversi manual
        'satellites' => 'integer',
        'created_at' => 'datetime', // Eloquent biasanya handle ini otomatis
        'updated_at' => 'datetime', // Eloquent biasanya handle ini otomatis
    ];

    // Anda bisa mendefinisikan relasi di sini jika diperlukan,
    // misalnya jika satu device memiliki banyak lokasi:
    // public function device()
    // {
    //     return $this->belongsTo(Device::class, 'device_id', 'device_identifier_column_di_tabel_devices');
    // }
}