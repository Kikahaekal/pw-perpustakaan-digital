<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserLoanController extends Controller
{
    /**
     * Daftar peminjaman milik user yang sedang login.
     */
    public function index(Request $request): View
    {
        $peminjamans = Peminjaman::query()
            ->with('buku.kategoris')
            ->where('user_id', $request->user()->id)
            ->orderByDesc('tanggal_pinjam')
            ->get();

        return view('loans.index', [
            'peminjamans' => $peminjamans,
            'q' => '',
        ]);
    }

    /**
     * Proses pinjam buku: validasi stok, catat transaksi, kurangi stok.
     */
    public function store(Request $request, Buku $buku): RedirectResponse
    {
        DB::transaction(function () use ($request, $buku): void {
            $locked = Buku::query()->lockForUpdate()->findOrFail($buku->id);

            $masihDipinjam = Peminjaman::query()
                ->where('user_id', $request->user()->id)
                ->where('buku_id', $locked->id)
                ->where('status', 'dipinjam')
                ->exists();

            if ($masihDipinjam) {
                throw ValidationException::withMessages([
                    'buku' => 'Anda masih meminjam buku ini. Kembalikan terlebih dahulu.',
                ]);
            }

            if ($locked->stok < 1) {
                throw ValidationException::withMessages([
                    'buku' => 'Stok Buku Habis',
                ]);
            }

            $locked->decrement('stok');

            Peminjaman::query()->create([
                'user_id' => $request->user()->id,
                'buku_id' => $locked->id,
                'tanggal_pinjam' => today(),
                'tenggat' => today()->addDays(14),
                'status' => 'dipinjam',
                'denda' => 0,
            ]);
        });

        return back()->with('status', 'Buku berhasil dipinjam. Selamat membaca!');
    }

    /**
     * Kembalikan buku: ubah status, isi tanggal kembali, tambah stok.
     */
    public function returnBook(Request $request, Peminjaman $peminjaman): RedirectResponse
    {
        abort_unless(
            $peminjaman->user_id === $request->user()->id || $request->user()->role === 'admin',
            403
        );

        DB::transaction(function () use ($peminjaman): void {
            $locked = Peminjaman::query()->lockForUpdate()->findOrFail($peminjaman->id);

            if ($locked->status !== 'dipinjam') {
                throw ValidationException::withMessages([
                    'peminjaman' => 'Peminjaman ini sudah dikembalikan.',
                ]);
            }

            $buku = Buku::query()->lockForUpdate()->findOrFail($locked->buku_id);

            $locked->update([
                'status' => 'dikembalikan',
                'tanggal_kembali' => today(),
            ]);

            $buku->increment('stok');
        });

        return back()->with('status', 'Buku berhasil dikembalikan. Terima kasih!');
    }
}
