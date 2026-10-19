<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            KategoriSeeder::class,
            BukuSeeder::class,
        ]);

        User::factory()->create([
            'fullname' => 'Anggota Perpus',
            'email' => 'user@perpus.test',
            'password' => 'password123',
            'role' => 'user',
        ]);

        User::factory()->create([
            'fullname' => 'Admin Perpus',
            'email' => 'admin@perpus.test',
            'password' => 'password123',
            'role' => 'admin',
        ]);
    }
}
