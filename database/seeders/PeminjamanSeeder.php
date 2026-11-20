<?php

namespace Database\Seeders;

use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Database\Seeder;

class PeminjamanSeeder extends Seeder
{
    /**
     * Riwayat peminjaman contoh agar grafik dashboard admin terisi.
     */
    public function run(): void
    {
        Peminjaman::query()->delete();

        collect(range(1, 8))->each(function (int $nomor): void {
            User::query()->firstOrCreate(
                ['email' => "anggota{$nomor}@perpus.test"],
                [
                    'fullname' => 'Anggota Demo '.$nomor,
                    'password' => 'password123',
                    'role' => 'user',
                ]
            );
        });

        $userIds = User::query()->where('role', 'user')->pluck('id');
        $bukuIds = Buku::query()->pluck('id');

        if ($userIds->isEmpty() || $bukuIds->isEmpty()) {
            return;
        }

        for ($mundur = 11; $mundur >= 0; $mundur--) {
            $awalBulan = now()->startOfMonth()->subMonths($mundur);
            $hariMaksimal = $mundur === 0 ? now()->day : $awalBulan->daysInMonth;
            $jumlah = $mundur === 0 ? rand(8, 12) : rand(4, 9);

            for ($i = 0; $i < $jumlah; $i++) {
                $pinjam = $awalBulan->copy()->addDays(rand(0, max(0, $hariMaksimal - 1)));
                $kembali = $pinjam->copy()->addDays(rand(2, 12));

                Peminjaman::query()->create([
                    'user_id' => $userIds->random(),
                    'buku_id' => $bukuIds->random(),
                    'tanggal_pinjam' => $pinjam->toDateString(),
                    'tenggat' => $pinjam->copy()->addDays(14)->toDateString(),
                    'tanggal_kembali' => $kembali->greaterThan(now()) ? now()->toDateString() : $kembali->toDateString(),
                    'status' => 'dikembalikan',
                    'denda' => 0,
                ]);
            }
        }
    }
}
