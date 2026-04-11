<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * 
     * PENTING: Jalankan seeder ini dengan perintah:
     * php artisan db:seed
     * 
     * Seeder ini akan mengisi:
     * - User test account (email: test@example.com, password: password)
     * - Data produk furniture (dari ProductSeeder)
     */
    public function run(): void
    {
        // Buat user test
        \App\Models\User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Jalankan ProductSeeder untuk mengisi data produk
        $this->call([
            ProductSeeder::class,
        ]);
        
        $this->command->info('✅ Seeder selesai! User dan produk berhasil dibuat.');
        $this->command->warn('📝 Login dengan: test@example.com / password');
    }
}