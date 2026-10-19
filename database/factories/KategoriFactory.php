<?php

namespace Database\Factories;

use App\Models\Kategori;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Kategori>
 */
class KategoriFactory extends Factory
{
    /**
     * Daftar kategori buku.
     *
     * @var list<string>
     */
    private const NAMA = [
        'Horror',
        'Thriller',
        'Fantasi',
        'Misteri',
        'Romance',
        'Drama',
        'Sejarah',
        'Edukasi',
        'Teknologi',
        'Aksi',
        'Komedi',
        'Psikologi',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_kategori' => fake()->unique()->randomElement(self::NAMA),
        ];
    }
}
