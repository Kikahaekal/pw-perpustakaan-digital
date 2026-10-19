<?php

namespace Database\Factories;

use App\Models\Buku;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Buku>
 */
class BukuFactory extends Factory
{
    /**
     * Palet gradasi cover yang dipakai desain NEVERMORE.
     *
     * @var list<string>
     */
    private const COVERS = [
        'from-red-900 via-black to-red-950',
        'from-amber-100 via-stone-300 to-stone-500',
        'from-slate-600 via-slate-800 to-black',
        'from-orange-400 via-orange-500 to-amber-600',
        'from-sky-800 via-slate-700 to-slate-900',
        'from-indigo-500 via-blue-600 to-blue-800',
        'from-cyan-600 via-sky-700 to-blue-900',
        'from-rose-400 via-orange-400 to-amber-500',
        'from-zinc-700 via-zinc-900 to-black',
        'from-violet-700 via-purple-900 to-black',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $judul = Str::title(rtrim(fake()->unique()->words(3, true), '.'));

        return [
            'judul' => $judul,
            'inisial' => Str::upper(Str::substr(collect(explode(' ', $judul))->map(fn (string $kata): string => $kata[0])->implode(''), 0, 3)),
            'cover' => fake()->randomElement(self::COVERS),
            'pengarang' => fake()->name(),
            'penerbit' => fake()->company(),
            'isbn' => fake()->ean13(),
            'tahun_terbit' => fake()->numberBetween(1990, (int) date('Y')),
            'jumlah_halaman' => fake()->numberBetween(80, 900),
            'stok' => fake()->numberBetween(0, 10),
            'populer' => fake()->numberBetween(50, 99),
            'jumlah_favorit' => fake()->numberBetween(100, 20000),
        ];
    }
}
