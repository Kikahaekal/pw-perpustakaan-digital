<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Data dummy tahap 1 — ganti query `bukus` + `kategoris` di tahap 2.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function dummyBooks(): array
    {
        return [
            ['id' => 666, 'judul' => 'Tales Of Horror', 'author' => 'HP. Lovecraft', 'author_books' => 70, 'tags' => [['nama' => 'Horror', 'count' => '20.1k'], ['nama' => 'Thriller', 'count' => '10k'], ['nama' => 'Dark Fantasy', 'count' => '35.1k']], 'halaman' => 768, 'uploaded' => '1 Month 2 Weeks 3 Days ago', 'favorit' => 19465, 'popular' => 99, 'tanggal_unggah' => '2026-09-28', 'stok' => 3, 'cover' => 'from-red-900 via-black to-red-950', 'inisial' => 'HPL'],
            ['id' => 101, 'judul' => 'The Night Ocean', 'author' => 'Paul La Farge', 'author_books' => 12, 'tags' => [['nama' => 'Horror', 'count' => '8.2k'], ['nama' => 'Misteri', 'count' => '5.1k']], 'halaman' => 320, 'uploaded' => '2 Weeks 1 Day ago', 'favorit' => 8210, 'popular' => 95, 'tanggal_unggah' => '2026-09-20', 'stok' => 4, 'cover' => 'from-amber-100 via-stone-300 to-stone-500', 'inisial' => 'TNO'],
            ['id' => 102, 'judul' => 'I Died During The College Debate', 'author' => 'Ren Takahashi', 'author_books' => 8, 'tags' => [['nama' => 'Fantasi', 'count' => '12k'], ['nama' => 'Aksi', 'count' => '9k']], 'halaman' => 412, 'uploaded' => '3 Days ago', 'favorit' => 15020, 'popular' => 93, 'tanggal_unggah' => '2026-10-04', 'stok' => 5, 'cover' => 'from-slate-600 via-slate-800 to-black', 'inisial' => 'IDD'],
            ['id' => 103, 'judul' => 'Tapes Und Butterbrot', 'author' => 'Studio Mono', 'author_books' => 3, 'tags' => [['nama' => 'Thriller', 'count' => '4k'], ['nama' => 'Psikologi', 'count' => '2k']], 'halaman' => 256, 'uploaded' => '1 Month ago', 'favorit' => 4310, 'popular' => 88, 'tanggal_unggah' => '2026-09-05', 'stok' => 0, 'cover' => 'from-orange-400 via-orange-500 to-amber-600', 'inisial' => 'TUB'],
            ['id' => 104, 'judul' => 'Roaches In Her Crib', 'author' => 'D. Coleman', 'author_books' => 5, 'tags' => [['nama' => 'Horror', 'count' => '6.7k'], ['nama' => 'Komedi', 'count' => '3k']], 'halaman' => 288, 'uploaded' => '5 Days ago', 'favorit' => 9870, 'popular' => 91, 'tanggal_unggah' => '2026-10-02', 'stok' => 2, 'cover' => 'from-sky-800 via-slate-700 to-slate-900', 'inisial' => 'RHC'],
            ['id' => 105, 'judul' => 'Dasar Pemrograman Web', 'author' => 'Tim Dosen PW', 'author_books' => 4, 'tags' => [['nama' => 'Edukasi', 'count' => '15k'], ['nama' => 'Teknologi', 'count' => '11k']], 'halaman' => 350, 'uploaded' => '1 Week ago', 'favorit' => 2210, 'popular' => 82, 'tanggal_unggah' => '2026-09-30', 'stok' => 10, 'cover' => 'from-indigo-500 via-blue-600 to-blue-800', 'inisial' => 'PW'],
            ['id' => 106, 'judul' => 'Laut Bercerita', 'author' => 'Leila S. Chudori', 'author_books' => 9, 'tags' => [['nama' => 'Drama', 'count' => '18k'], ['nama' => 'Sejarah', 'count' => '7k']], 'halaman' => 379, 'uploaded' => '2 Months ago', 'favorit' => 17400, 'popular' => 90, 'tanggal_unggah' => '2026-08-15', 'stok' => 6, 'cover' => 'from-cyan-600 via-sky-700 to-blue-900', 'inisial' => 'LB'],
            ['id' => 107, 'judul' => 'Senja di Ujung Kota', 'author' => 'M. Fauzi', 'author_books' => 2, 'tags' => [['nama' => 'Romance', 'count' => '9k'], ['nama' => 'Drama', 'count' => '4k']], 'halaman' => 198, 'uploaded' => '3 Weeks ago', 'favorit' => 3120, 'popular' => 77, 'tanggal_unggah' => '2026-09-12', 'stok' => 7, 'cover' => 'from-rose-400 via-orange-400 to-amber-500', 'inisial' => 'SUK'],
            ['id' => 108, 'judul' => 'Operasi Tengah Malam', 'author' => 'Kika Haekal', 'author_books' => 6, 'tags' => [['nama' => 'Thriller', 'count' => '7k'], ['nama' => 'Aksi', 'count' => '6k']], 'halaman' => 334, 'uploaded' => '1 Day ago', 'favorit' => 5540, 'popular' => 86, 'tanggal_unggah' => '2026-10-06', 'stok' => 3, 'cover' => 'from-zinc-700 via-zinc-900 to-black', 'inisial' => 'OTM'],
            ['id' => 109, 'judul' => 'Misteri Kos No. 13', 'author' => 'M. Khaerul', 'author_books' => 4, 'tags' => [['nama' => 'Horror', 'count' => '11k'], ['nama' => 'Misteri', 'count' => '8k']], 'halaman' => 288, 'uploaded' => '2 Days ago', 'favorit' => 7630, 'popular' => 89, 'tanggal_unggah' => '2026-10-05', 'stok' => 5, 'cover' => 'from-violet-700 via-purple-900 to-black', 'inisial' => 'K13'],
        ];
    }

    public static function findBook(int $id): ?array
    {
        foreach (self::dummyBooks() as $book) {
            if ($book['id'] === $id) {
                return $book;
            }
        }

        return null;
    }

    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $tag = trim((string) $request->query('tag', ''));
        $author = trim((string) $request->query('author', ''));
        $onlyFavorites = $request->boolean('favorites');
        $favorites = $request->session()->get('favorites', []);

        $books = collect(self::dummyBooks())
            ->when($q !== '', fn ($c) => $c->filter(
                fn ($b) => str_contains(strtolower($b['judul'].' '.$b['author']), strtolower($q))
            ))
            ->when($tag !== '', fn ($c) => $c->filter(
                fn ($b) => collect($b['tags'])->contains(fn ($t) => strtolower($t['nama']) === strtolower($tag))
            ))
            ->when($author !== '', fn ($c) => $c->filter(
                fn ($b) => str_contains(strtolower($b['author']), strtolower($author))
            ))
            ->when($onlyFavorites, fn ($c) => $c->filter(
                fn ($b) => in_array($b['id'], $favorites, true)
            ))
            ->values();

        $allTags = collect(self::dummyBooks())->pluck('tags')->flatten(1)->pluck('nama')->unique()->sort()->values();
        $allAuthors = collect(self::dummyBooks())->pluck('author')->unique()->sort()->values();

        $isFiltering = $q !== '' || $tag !== '' || $author !== '' || $onlyFavorites;

        return view('dashboard.index', [
            'books' => $books,
            'popular' => $isFiltering ? $books->take(5)->values() : (clone $books)->sortByDesc('popular')->take(5)->values(),
            'newUploads' => (clone $books)->sortByDesc('tanggal_unggah')->take(10)->values(),
            'random' => (clone $books)->shuffle()->take(5)->values(),
            'tags' => $allTags,
            'authors' => $allAuthors,
            'q' => $q,
            'activeTag' => $tag,
            'activeAuthor' => $author,
            'onlyFavorites' => $onlyFavorites,
            'favorites' => $favorites,
        ]);
    }

    public function show(Request $request, int $id): View
    {
        $book = self::findBook($id);

        abort_if($book === null, 404);

        $favorites = $request->session()->get('favorites', []);
        $book['is_favorit'] = in_array($book['id'], $favorites, true);

        $newUploads = collect(self::dummyBooks())->sortByDesc('tanggal_unggah')->take(10)->values();

        return view('dashboard.show', [
            'book' => $book,
            'newUploads' => $newUploads,
            'q' => '',
        ]);
    }

    public function toggleFavorite(Request $request, int $id): RedirectResponse
    {
        abort_if(self::findBook($id) === null, 404);

        $favorites = $request->session()->get('favorites', []);

        if (in_array($id, $favorites, true)) {
            $favorites = array_values(array_diff($favorites, [$id]));
        } else {
            $favorites[] = $id;
        }

        $request->session()->put('favorites', $favorites);

        return back();
    }
}
