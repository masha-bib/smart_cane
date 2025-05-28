<?php

// app/Models/RekamanObjek.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekamanObjek extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang berasosiasi dengan model.
     * PENTING: Ganti 'rekaman_objek' dengan nama tabel Anda yang sebenarnya.
     *
     * @var string
     */
    protected $table = 'deteksi_objek'; 

    /**
     * Menunjukkan apakah model harus memiliki timestamps (created_at, updated_at).
     * Karena tabel Anda memiliki kolom 'waktu' sendiri dan tidak ada 'created_at'/'updated_at' standar Laravel,
     * kita set ini ke false.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Atribut yang dapat diisi secara massal (jika Anda membuat fitur untuk input data).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_file',
        'kategori',
        'waktu',
    ];

    /**
     * The attributes that should be cast.
     * Ini membantu Laravel memperlakukan kolom 'waktu' sebagai objek tanggal/waktu (Carbon).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'waktu' => 'datetime',
    ];
}
