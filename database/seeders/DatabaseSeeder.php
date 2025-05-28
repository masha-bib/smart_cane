<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create(); // Anda bisa mengaktifkan ini jika perlu banyak user contoh

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Panggil seeder lain yang Anda buat di sini
        $this->call([
            DeteksiObjekSeeder::class, // <--- TAMBAHKAN BARIS INI
            // Jika ada seeder lain, tambahkan juga di sini, contoh:
            // AnotherSeeder::class,
        ]);
    }
}