<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class BookController extends Controller
{
    /**
     * Simpan buku baru dari modal "Add New Book".
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        $buku = DB::transaction(function () use ($request, $validated): Buku {
            $data = array_merge([
                'penerbit' => 'Tidak diketahui',
                'tahun_terbit' => (int) now()->year,
                'stok' => 1,
            ], $this->bookPayload($validated));

            $data['inisial'] ??= $this->makeInitials($validated['judul']);
            $data['seksi'] = $validated['seksi'] ?? null;
            $data += $this->storeCover($request);

            $buku = Buku::query()->create($data);
            $buku->kategoris()->sync($this->syncTags($validated['tags'] ?? null));

            return $buku;
        });

        return redirect()
            ->route('admin.dashboard')
            ->with('status', 'Buku "'.$buku->judul.'" berhasil ditambahkan.');
    }

    /**
     * Perbarui buku yang sudah ada lewat modal edit.
     */
    public function update(Request $request, Buku $buku): RedirectResponse
    {
        $validated = $request->validate($this->rules($buku));

        DB::transaction(function () use ($request, $validated, $buku): void {
            $data = $this->bookPayload($validated);

            if (array_key_exists('seksi', $validated)) {
                $data['seksi'] = $validated['seksi'];
            }

            if ($request->hasFile('cover_image')) {
                $data = array_merge($data, $this->storeCover($request));

                if ($buku->cover_image) {
                    Storage::disk('public')->delete($buku->cover_image);
                }
            }

            $buku->update($data);

            if (array_key_exists('tags', $validated)) {
                $buku->kategoris()->sync($this->syncTags($validated['tags']));
            }
        });

        return redirect()
            ->route('admin.dashboard')
            ->with('status', 'Buku "'.$buku->judul.'" berhasil diperbarui.');
    }

    /**
     * Hapus buku. Buku yang sudah punya riwayat peminjaman tidak boleh dihapus.
     */
    public function destroy(Buku $buku): RedirectResponse
    {
        if ($buku->peminjamans()->exists()) {
            return redirect()
                ->route('admin.dashboard')
                ->with('error', 'Buku "'.$buku->judul.'" tidak dapat dihapus karena memiliki riwayat peminjaman.');
        }

        DB::transaction(function () use ($buku): void {
            if ($buku->cover_image) {
                Storage::disk('public')->delete($buku->cover_image);
            }

            $buku->kategoris()->detach();
            $buku->delete();
        });

        return redirect()
            ->route('admin.dashboard')
            ->with('status', 'Buku "'.$buku->judul.'" berhasil dihapus.');
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function bookPayload(array $validated): array
    {
        $fields = ['judul', 'pengarang', 'seksi', 'penerbit', 'isbn', 'tahun_terbit', 'jumlah_halaman', 'stok', 'inisial'];
        $data = [];

        foreach ($fields as $field) {
            if (array_key_exists($field, $validated) && $validated[$field] !== null && $validated[$field] !== '') {
                $data[$field] = $validated[$field];
            }
        }

        return $data;
    }

    /**
     * Simpan file cover ke disk publik dan kembalikan atribut cover_image.
     *
     * @return array<string, string>
     */
    private function storeCover(Request $request): array
    {
        if (! $request->hasFile('cover_image')) {
            return [];
        }

        return ['cover_image' => $request->file('cover_image')->store('covers', 'public')];
    }

    /**
     * Pecah input "Tags" (dipisah koma) menjadi id kategori, buat yang belum ada.
     *
     * @return list<int>
     */
    private function syncTags(?string $tags): array
    {
        return collect(explode(',', (string) $tags))
            ->map(fn (string $tag): string => trim($tag))
            ->filter()
            ->unique(fn (string $tag): string => mb_strtolower($tag))
            ->map(fn (string $tag): int => Kategori::query()->firstOrCreate(['nama_kategori' => $tag])->id)
            ->values()
            ->all();
    }

    private function makeInitials(string $judul): string
    {
        return mb_strtoupper(
            mb_substr(
                collect(preg_split('/\s+/', trim($judul)) ?: [])
                    ->filter()
                    ->map(fn (string $kata): string => mb_substr($kata, 0, 1))
                    ->implode(''),
                0,
                3
            )
        );
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function rules(?Buku $buku = null): array
    {
        $required = $buku === null ? 'required' : 'sometimes';

        return [
            'judul' => [$required, 'string', 'max:255'],
            'pengarang' => [$required, 'string', 'max:255'],
            'tags' => ['nullable', 'string', 'max:1000'],
            'seksi' => ['nullable', 'string', Rule::in(array_keys(Buku::SEKSI_LABELS))],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'penerbit' => ['nullable', 'string', 'max:255'],
            'isbn' => ['nullable', 'string', 'max:255'],
            'tahun_terbit' => ['nullable', 'integer', 'min:0', 'max:'.now()->year],
            'jumlah_halaman' => ['nullable', 'integer', 'min:1'],
            'stok' => ['nullable', 'integer', 'min:0'],
            'inisial' => ['nullable', 'string', 'max:8'],
        ];
    }
}
