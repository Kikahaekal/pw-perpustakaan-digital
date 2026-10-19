<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    /**
     * Kategori yang dipakai katalog NEVERMORE.
     *
     * @var list<string>
     */
    private const KATEGORI = [
        'Horror',
        'Thriller',
        'Dark Fantasy',
        'Misteri',
        'Fantasi',
        'Aksi',
        'Psikologi',
        'Komedi',
        'Edukasi',
        'Teknologi',
        'Drama',
        'Sejarah',
        'Romance',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (self::KATEGORI as $nama) {
            Kategori::firstOrCreate(['nama_kategori' => $nama]);
        }
    }
}
