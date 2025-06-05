<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekamanObjek extends Model
{
    use HasFactory;
    protected $table = 'deteksi_objek'; 

    public $timestamps = false;

    protected $fillable = [
        'nama_file',
        'kategori',
        'waktu',
    ];

    protected $casts = [
        'waktu' => 'datetime',
    ];
}
