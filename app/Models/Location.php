<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'latitude',
        'longitude',
        'description',
        'device_id',
        'satellites',
        'hdop',
        'image_url',    // TAMBAHKAN INI
        'image_path',   // TAMBAHKAN INI
        // 'event_detected', // Tambahkan jika ada di migrasi
        // 'voice_alert',  // Tambahkan jika ada di migrasi
    ];

    protected $casts = [
        // Anda menggunakan decimal(10,7) di DB, jadi casting ke string atau float bisa jadi pilihan.
        // Jika ingin tetap sebagai string untuk presisi:
        'latitude' => 'string',
        'longitude' => 'string',
        // Atau jika float cukup:
        // 'latitude' => 'float',
        // 'longitude' => 'float',
        'hdop' => 'float',
        'satellites' => 'integer', // Pastikan ini integer di DB atau sesuaikan cast
    ];
}   