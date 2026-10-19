<?php

namespace Database\Seeders;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BukuSeeder extends Seeder
{
    /**
     * Katalog contoh NEVERMORE (id, judul, pengarang, dst).
     *
     * @var list<array<string, mixed>>
     */
    private const BUKU = [
        ['judul' => 'Tales Of Horror', 'inisial' => 'HPL', 'cover' => 'from-red-900 via-black to-red-950', 'pengarang' => 'HP. Lovecraft', 'penerbit' => 'Arkham House', 'isbn' => '978-0-000-00001-1', 'tahun_terbit' => 1939, 'jumlah_halaman' => 768, 'stok' => 3, 'populer' => 99, 'jumlah_favorit' => 19465, 'hari' => 44, 'kategoris' => ['Horror', 'Thriller', 'Dark Fantasy']],
        ['judul' => 'The Night Ocean', 'inisial' => 'TNO', 'cover' => 'from-amber-100 via-stone-300 to-stone-500', 'pengarang' => 'Paul La Farge', 'penerbit' => 'Penguin Press', 'isbn' => '978-0-000-00002-8', 'tahun_terbit' => 2017, 'jumlah_halaman' => 320, 'stok' => 4, 'populer' => 95, 'jumlah_favorit' => 8210, 'hari' => 15, 'kategoris' => ['Horror', 'Misteri']],
        ['judul' => 'I Died During The College Debate', 'inisial' => 'IDD', 'cover' => 'from-slate-600 via-slate-800 to-black', 'pengarang' => 'Ren Takahashi', 'penerbit' => 'Shueisha', 'isbn' => '978-0-000-00003-5', 'tahun_terbit' => 2021, 'jumlah_halaman' => 412, 'stok' => 5, 'populer' => 93, 'jumlah_favorit' => 15020, 'hari' => 3, 'kategoris' => ['Fantasi', 'Aksi']],
        ['judul' => 'Tapes Und Butterbrot', 'inisial' => 'TUB', 'cover' => 'from-orange-400 via-orange-500 to-amber-600', 'pengarang' => 'Studio Mono', 'penerbit' => 'Mono Press', 'isbn' => '978-0-000-00004-2', 'tahun_terbit' => 2019, 'jumlah_halaman' => 256, 'stok' => 0, 'populer' => 88, 'jumlah_favorit' => 4310, 'hari' => 30, 'kategoris' => ['Thriller', 'Psikologi']],
        ['judul' => 'Roaches In Her Crib', 'inisial' => 'RHC', 'cover' => 'from-sky-800 via-slate-700 to-slate-900', 'pengarang' => 'D. Coleman', 'penerbit' => 'Nightfall Books', 'isbn' => '978-0-000-00005-9', 'tahun_terbit' => 2020, 'jumlah_halaman' => 288, 'stok' => 2, 'populer' => 91, 'jumlah_favorit' => 9870, 'hari' => 5, 'kategoris' => ['Horror', 'Komedi']],
        ['judul' => 'Dasar Pemrograman Web', 'inisial' => 'PW', 'cover' => 'from-indigo-500 via-blue-600 to-blue-800', 'pengarang' => 'Tim Dosen PW', 'penerbit' => 'UMRAH Press', 'isbn' => '978-0-000-00006-6', 'tahun_terbit' => 2024, 'jumlah_halaman' => 350, 'stok' => 10, 'populer' => 82, 'jumlah_favorit' => 2210, 'hari' => 7, 'kategoris' => ['Edukasi', 'Teknologi']],
        ['judul' => 'Laut Bercerita', 'inisial' => 'LB', 'cover' => 'from-cyan-600 via-sky-700 to-blue-900', 'pengarang' => 'Leila S. Chudori', 'penerbit' => 'Kepustakaan Populer Gramedia', 'isbn' => '978-0-000-00007-3', 'tahun_terbit' => 2017, 'jumlah_halaman' => 379, 'stok' => 6, 'populer' => 90, 'jumlah_favorit' => 17400, 'hari' => 60, 'kategoris' => ['Drama', 'Sejarah']],
        ['judul' => 'Senja di Ujung Kota', 'inisial' => 'SUK', 'cover' => 'from-rose-400 via-orange-400 to-amber-500', 'pengarang' => 'M. Fauzi', 'penerbit' => 'Indie Publisher', 'isbn' => '978-0-000-00008-0', 'tahun_terbit' => 2023, 'jumlah_halaman' => 198, 'stok' => 7, 'populer' => 77, 'jumlah_favorit' => 3120, 'hari' => 21, 'kategoris' => ['Romance', 'Drama']],
        ['judul' => 'Operasi Tengah Malam', 'inisial' => 'OTM', 'cover' => 'from-zinc-700 via-zinc-900 to-black', 'pengarang' => 'Kika Haekal', 'penerbit' => 'Indie Publisher', 'isbn' => '978-0-000-00009-7', 'tahun_terbit' => 2025, 'jumlah_halaman' => 334, 'stok' => 3, 'populer' => 86, 'jumlah_favorit' => 5540, 'hari' => 1, 'kategoris' => ['Thriller', 'Aksi']],
        ['judul' => 'Misteri Kos No. 13', 'inisial' => 'K13', 'cover' => 'from-violet-700 via-purple-900 to-black', 'pengarang' => 'M. Khaerul', 'penerbit' => 'Indie Publisher', 'isbn' => '978-0-000-00010-3', 'tahun_terbit' => 2025, 'jumlah_halaman' => 288, 'stok' => 5, 'populer' => 89, 'jumlah_favorit' => 7630, 'hari' => 2, 'kategoris' => ['Horror', 'Misteri']],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('buku_kategoris')->delete();
        Buku::query()->delete();

        foreach (self::BUKU as $data) {
            $unggah = now()->subDays($data['hari']);
            $kategoris = $data['kategoris'];
            unset($data['hari'], $data['kategoris']);

            $buku = new Buku($data);
            $buku->created_at = $unggah;
            $buku->updated_at = $unggah;
            $buku->save();

            $buku->kategoris()->sync(
                Kategori::whereIn('nama_kategori', $kategoris)->pluck('id')->all()
            );
        }
    }
}
