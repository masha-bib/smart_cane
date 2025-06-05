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
        'device_id',    
        'latitude',     
        'longitude',    
        'satellites',   
        'hdop',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'latitude' => 'string',
        'longitude' => 'string',

        'hdop' => 'float',       
        'satellites' => 'integer',
        'created_at' => 'datetime', 
        'updated_at' => 'datetime', 
    ];
}