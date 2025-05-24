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
        Schema::table('locations', function (Blueprint $table) {
            // Menambahkan kolom setelah 'hdop' (atau kolom terakhir yang relevan sebelum timestamp)
            $table->string('image_url')->after('hdop')->nullable()->comment('URL publik ke gambar dari ESP32-CAM');
            $table->string('image_path')->after('image_url')->nullable()->comment('Path relatif gambar di storage server');
            // Jika Anda ingin event dan voice alert juga, tambahkan di sini:
            // $table->string('event_detected')->after('image_path')->nullable();
            // $table->string('voice_alert')->after('event_detected')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->dropColumn(['image_url', 'image_path']);
            // $table->dropColumn(['event_detected', 'voice_alert']); // Jika ditambahkan di atas
        });
    }
};