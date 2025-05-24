<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id(); // Primary key auto-increment
            // --- TAMBAHKAN KOLOM-KOLOM DI BAWAH INI ---
            $table->string('device_id');         // Untuk identifikasi tongkat
            $table->decimal('latitude', 10, 7);  // Total 10 digit, 7 di belakang koma
            $table->decimal('longitude', 10, 7); // Total 10 digit, 7 di belakang koma
            $table->integer('satellites')->nullable(); // Jumlah satelit, bisa null
            $table->float('hdop')->nullable();         // Horizontal Dilution of Precision, bisa null
            // --- AKHIR PENAMBAHAN KOLOM ---
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};