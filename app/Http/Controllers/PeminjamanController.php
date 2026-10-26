<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PeminjamanController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $peminjamans = Peminjaman::query()
            ->with(['user', 'buku.kategoris'])
            ->when(
                $request->user()?->role !== 'admin',
                fn ($query) => $query->where('user_id', $request->user()->id)
            )
            ->latest()
            ->paginate(15);

        return response()->json($peminjamans);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'buku_id' => ['required', 'integer', Rule::exists('bukus', 'id')],
            'tenggat' => ['required', 'date', 'after_or_equal:today'],
        ]);

        $peminjaman = DB::transaction(function () use ($request, $validated): Peminjaman {
            $buku = Buku::query()->lockForUpdate()->findOrFail($validated['buku_id']);

            if ($buku->stok < 1) {
                throw ValidationException::withMessages([
                    'buku_id' => 'Stok buku sedang habis.',
                ]);
            }

            $buku->decrement('stok');

            return Peminjaman::query()->create([
                'user_id' => $request->user()->id,
                'buku_id' => $buku->id,
                'tanggal_pinjam' => today(),
                'tenggat' => $validated['tenggat'],
                'status' => 'dipinjam',
                'denda' => 0,
            ])->load(['user', 'buku.kategoris']);
        });

        return response()->json($peminjaman, 201);
    }

    public function show(Request $request, Peminjaman $peminjaman): JsonResponse
    {
        $this->authorizeOwnerOrAdmin($request, $peminjaman);

        return response()->json($peminjaman->load(['user', 'buku.kategoris']));
    }

    public function update(Request $request, Peminjaman $peminjaman): JsonResponse
    {
        $this->authorizeOwnerOrAdmin($request, $peminjaman);
        $request->validate([
            'status' => ['required', Rule::in(['dikembalikan'])],
        ]);

        $result = DB::transaction(function () use ($peminjaman): Peminjaman|JsonResponse {
            $lockedPeminjaman = Peminjaman::query()->lockForUpdate()->findOrFail($peminjaman->id);

            if ($lockedPeminjaman->status === 'dikembalikan') {
                return response()->json([
                    'message' => 'Peminjaman ini sudah dikembalikan.',
                ], 409);
            }

            $buku = Buku::query()->lockForUpdate()->findOrFail($lockedPeminjaman->buku_id);
            $lockedPeminjaman->update([
                'status' => 'dikembalikan',
                'tanggal_kembali' => today(),
            ]);
            $buku->increment('stok');

            return $lockedPeminjaman->load(['user', 'buku.kategoris']);
        });

        return $result instanceof JsonResponse ? $result : response()->json($result);
    }

    public function destroy(Request $request, Peminjaman $peminjaman): JsonResponse
    {
        $this->authorizeOwnerOrAdmin($request, $peminjaman);

        DB::transaction(function () use ($peminjaman): void {
            $lockedPeminjaman = Peminjaman::query()->lockForUpdate()->findOrFail($peminjaman->id);

            if ($lockedPeminjaman->status === 'dipinjam') {
                Buku::query()->lockForUpdate()
                    ->findOrFail($lockedPeminjaman->buku_id)
                    ->increment('stok');
            }

            $lockedPeminjaman->delete();
        });

        return response()->json(status: 204);
    }

    private function authorizeOwnerOrAdmin(Request $request, Peminjaman $peminjaman): void
    {
        abort_unless(
            $request->user()?->role === 'admin' || $request->user()?->id === $peminjaman->user_id,
            403
        );
    }
}
