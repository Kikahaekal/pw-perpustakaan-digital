<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BukuController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Buku::query()->with('kategoris')->latest()->paginate(15)
        );
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $validated = $request->validate($this->rules());
        $kategoriIds = $validated['kategori_ids'] ?? [];
        unset($validated['kategori_ids']);

        $buku = DB::transaction(function () use ($validated, $kategoriIds): Buku {
            $buku = Buku::query()->create($validated);
            $buku->kategoris()->sync($kategoriIds);

            return $buku->load('kategoris');
        });

        return response()->json($buku, 201);
    }

    public function show(Buku $buku): JsonResponse
    {
        return response()->json($buku->load('kategoris'));
    }

    public function update(Request $request, Buku $buku): JsonResponse
    {
        $this->authorizeAdmin($request);
        $validated = $request->validate($this->rules($buku));
        $kategoriIds = $validated['kategori_ids'] ?? null;
        unset($validated['kategori_ids']);

        DB::transaction(function () use ($buku, $validated, $kategoriIds): void {
            $buku->update($validated);

            if ($kategoriIds !== null) {
                $buku->kategoris()->sync($kategoriIds);
            }
        });

        return response()->json($buku->refresh()->load('kategoris'));
    }

    public function destroy(Request $request, Buku $buku): JsonResponse
    {
        $this->authorizeAdmin($request);

        if ($buku->peminjamans()->exists()) {
            return response()->json([
                'message' => 'Buku yang sudah tercatat dalam peminjaman tidak dapat dihapus.',
            ], 409);
        }

        $buku->delete();

        return response()->json(status: 204);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function rules(?Buku $buku = null): array
    {
        $required = $buku === null ? 'required' : 'sometimes';

        return [
            'judul' => [$required, 'string', 'max:255'],
            'inisial' => ['sometimes', 'nullable', 'string', 'max:8'],
            'cover' => ['sometimes', 'nullable', 'string', 'max:255'],
            'pengarang' => [$required, 'string', 'max:255'],
            'penerbit' => [$required, 'string', 'max:255'],
            'isbn' => ['sometimes', 'nullable', 'string', 'max:255'],
            'tahun_terbit' => [$required, 'integer', 'min:0', 'max:'.now()->year],
            'jumlah_halaman' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'stok' => [$required, 'integer', 'min:0'],
            'populer' => ['sometimes', 'integer', 'min:0'],
            'jumlah_favorit' => ['sometimes', 'integer', 'min:0'],
            'kategori_ids' => ['sometimes', 'array'],
            'kategori_ids.*' => ['integer', 'distinct', Rule::exists('kategoris', 'id')],
        ];
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()?->role === 'admin', 403);
    }
}
