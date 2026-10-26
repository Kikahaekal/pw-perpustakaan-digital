<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KategoriController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Kategori::query()->withCount('bukus')->orderBy('nama_kategori')->paginate(15)
        );
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $validated = $request->validate([
            'nama_kategori' => ['required', 'string', 'max:255', Rule::unique('kategoris', 'nama_kategori')],
        ]);

        return response()->json(Kategori::query()->create($validated), 201);
    }

    public function show(Kategori $kategori): JsonResponse
    {
        return response()->json($kategori->load('bukus'));
    }

    public function update(Request $request, Kategori $kategori): JsonResponse
    {
        $this->authorizeAdmin($request);
        $validated = $request->validate([
            'nama_kategori' => [
                'required',
                'string',
                'max:255',
                Rule::unique('kategoris', 'nama_kategori')->ignore($kategori->id),
            ],
        ]);

        $kategori->update($validated);

        return response()->json($kategori->refresh());
    }

    public function destroy(Request $request, Kategori $kategori): JsonResponse
    {
        $this->authorizeAdmin($request);
        $kategori->delete();

        return response()->json(status: 204);
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()?->role === 'admin', 403);
    }
}
