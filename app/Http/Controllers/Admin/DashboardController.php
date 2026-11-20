<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard admin: statistik, grafik, dan riwayat unggahan.
     */
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $range = (int) $request->query('range', 12);
        $range = in_array($range, [6, 12], true) ? $range : 12;

        $bulan = collect(range($range - 1, 0))
            ->map(fn (int $mundur): Carbon => now()->startOfMonth()->subMonths($mundur));

        $peminjamen = Peminjaman::query()
            ->where('tanggal_pinjam', '>=', $bulan->first()->copy()->startOfMonth())
            ->get(['user_id', 'tanggal_pinjam']);

        $perBulan = $peminjamen->groupBy(
            fn (Peminjaman $peminjaman): string => $peminjaman->tanggal_pinjam->format('Y-m')
        );

        $buku = Buku::query()
            ->with('kategoris')
            ->when($q !== '', fn ($builder) => $builder->where(function ($query) use ($q) {
                $query->where('judul', 'like', "%{$q}%")
                    ->orWhere('pengarang', 'like', "%{$q}%");
            }))
            ->orderByDesc('created_at')
            ->limit($q !== '' ? 20 : 10)
            ->get();

        return view('admin.dashboard', [
            'q' => $q,
            'range' => $range,
            'books' => $buku,
            'stats' => [
                'titles' => Buku::query()->count(),
                'readers' => Peminjaman::query()->distinct()->count('user_id'),
                'monthly_loans' => Peminjaman::query()
                    ->whereBetween('tanggal_pinjam', [now()->startOfMonth(), now()->endOfMonth()])
                    ->count(),
                'flagged' => Buku::query()->where('stok', 0)->count(),
            ],
            'activeUsersChart' => [
                'labels' => $bulan->map(fn (Carbon $tanggal): string => $tanggal->format('M'))->all(),
                'values' => $this->chartValues($bulan, $perBulan, fn (Collection $items): int => $items->pluck('user_id')->unique()->count()),
            ],
            'borrowedChart' => [
                'labels' => $bulan->map(fn (Carbon $tanggal): string => $tanggal->format('M'))->all(),
                'values' => $this->chartValues($bulan, $perBulan, fn (Collection $items): int => $items->count()),
            ],
        ]);
    }

    /**
     * Susun deret angka per bulan agar selalu sejajar dengan label bulan.
     *
     * @param  Collection<int, Carbon>  $bulan
     * @param  Collection<string, Collection<int, Peminjaman>>  $perBulan
     * @param  callable(Collection<int, Peminjaman>): int  $hitung
     * @return list<int>
     */
    private function chartValues(Collection $bulan, Collection $perBulan, callable $hitung): array
    {
        return $bulan
            ->map(fn (Carbon $tanggal): int => $hitung($perBulan->get($tanggal->format('Y-m'), collect())))
            ->values()
            ->all();
    }
}
