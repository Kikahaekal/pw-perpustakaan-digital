<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Eager load kategori beserta jumlah bukunya (untuk label tag).
     *
     * @return array<string, callable>
     */
    private function kategorisRelation(): array
    {
        return [
            'kategoris' => fn ($query) => $query->withCount('bukus'),
        ];
    }

    /**
     * Jumlah buku per pengarang (kolom kecil di samping nama author).
     *
     * @return Collection<string, int>
     */
    private function authorBookCounts(): Collection
    {
        return Buku::query()
            ->selectRaw('pengarang, count(*) as total')
            ->groupBy('pengarang')
            ->pluck('total', 'pengarang');
    }

    /**
     * Ubah model Buku menjadi bentuk array yang dipakai view katalog.
     *
     * @param  array<int, int>  $favorites
     * @param  Collection<string, int>  $authorCounts
     * @return array<string, mixed>
     */
    private function present(Buku $buku, array $favorites, Collection $authorCounts): array
    {
        return [
            'id' => $buku->id,
            'judul' => $buku->judul,
            'author' => $buku->pengarang,
            'author_books' => (int) $authorCounts->get($buku->pengarang, 0),
            'tags' => $buku->kategoris->map(fn (Kategori $kategori): array => [
                'nama' => $kategori->nama_kategori,
                'count' => (string) $kategori->bukus_count,
            ])->all(),
            'halaman' => (int) $buku->jumlah_halaman,
            'uploaded' => $buku->created_at?->diffForHumans(),
            'favorit' => (int) $buku->jumlah_favorit,
            'popular' => (int) $buku->populer,
            'tanggal_unggah' => $buku->created_at?->toDateString(),
            'stok' => (int) $buku->stok,
            'cover' => $buku->cover ?? 'from-slate-700 via-slate-900 to-black',
            'inisial' => $buku->inisial ?? '',
            'is_favorit' => in_array($buku->id, $favorites, true),
        ];
    }

    /**
     * @param  EloquentCollection<int, Buku>|Collection<int, Buku>  $books
     * @param  array<int, int>  $favorites
     * @param  Collection<string, int>  $authorCounts
     * @return Collection<int, array<string, mixed>>
     */
    private function presentMany(EloquentCollection|Collection $books, array $favorites, Collection $authorCounts): Collection
    {
        return collect($books)
            ->map(fn (Buku $buku): array => $this->present($buku, $favorites, $authorCounts))
            ->values();
    }

    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $tag = trim((string) $request->query('tag', ''));
        $author = trim((string) $request->query('author', ''));
        $onlyFavorites = $request->boolean('favorites');
        $favorites = $request->session()->get('favorites', []);

        $books = Buku::query()
            ->with($this->kategorisRelation())
            ->when($q !== '', fn ($builder) => $builder->where(function ($query) use ($q) {
                $query->where('judul', 'like', "%{$q}%")
                    ->orWhere('pengarang', 'like', "%{$q}%");
            }))
            ->when($tag !== '', fn ($builder) => $builder->whereHas(
                'kategoris', fn ($query) => $query->where('nama_kategori', $tag)
            ))
            ->when($author !== '', fn ($builder) => $builder->where('pengarang', 'like', "%{$author}%"))
            ->when($onlyFavorites, fn ($builder) => $builder->whereIn('id', $favorites === [] ? [0] : $favorites))
            ->get();

        $isFiltering = $q !== '' || $tag !== '' || $author !== '' || $onlyFavorites;
        $authorCounts = $this->authorBookCounts();

        $popular = $isFiltering
            ? $books->take(5)
            : $books->sortByDesc('populer')->take(5)->values();

        return view('dashboard.index', [
            'books' => $this->presentMany($books, $favorites, $authorCounts),
            'popular' => $this->presentMany($popular, $favorites, $authorCounts),
            'newUploads' => $this->presentMany($books->sortByDesc('created_at')->take(10)->values(), $favorites, $authorCounts),
            'random' => $this->presentMany($books->shuffle()->take(5)->values(), $favorites, $authorCounts),
            'tags' => Kategori::query()->orderBy('nama_kategori')->pluck('nama_kategori'),
            'authors' => Buku::query()->orderBy('pengarang')->distinct()->pluck('pengarang'),
            'q' => $q,
            'activeTag' => $tag,
            'activeAuthor' => $author,
            'onlyFavorites' => $onlyFavorites,
            'favorites' => $favorites,
        ]);
    }

    public function show(Request $request, int $id): View
    {
        $buku = Buku::query()->with($this->kategorisRelation())->findOrFail($id);

        $favorites = $request->session()->get('favorites', []);
        $authorCounts = $this->authorBookCounts();

        $newUploads = Buku::query()
            ->with($this->kategorisRelation())
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('dashboard.show', [
            'book' => $this->present($buku, $favorites, $authorCounts),
            'newUploads' => $this->presentMany($newUploads, $favorites, $authorCounts),
            'q' => '',
        ]);
    }

    public function toggleFavorite(Request $request, int $id): RedirectResponse
    {
        abort_unless(Buku::query()->whereKey($id)->exists(), 404);

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
