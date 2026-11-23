<?php

use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user dapat meminjam buku dan grafik admin ikut berubah', function () {
    $user = User::factory()->create();
    $buku = Buku::factory()->create(['stok' => 1]);

    $this->actingAs($user)
        ->post(route('loans.store', $buku))
        ->assertRedirect()
        ->assertSessionHas('status');

    expect($buku->fresh()->stok)->toBe(0);

    $this->assertDatabaseHas('peminjamen', [
        'user_id' => $user->id,
        'buku_id' => $buku->id,
        'status' => 'dipinjam',
        'tanggal_kembali' => null,
    ]);

    $admin = User::factory()->create(['role' => 'admin']);
    $response = $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();

    $chart = $response->viewData('borrowedChart');
    expect($chart['values'][array_key_last($chart['values'])])->toBe(1);
    expect($response->viewData('stats')['monthly_loans'])->toBe(1);
});

test('user tidak bisa meminjam buku saat stok habis', function () {
    $user = User::factory()->create();
    $buku = Buku::factory()->create(['stok' => 0]);

    $this->actingAs($user)
        ->from(route('books.show', $buku->id))
        ->post(route('loans.store', $buku))
        ->assertRedirect(route('books.show', $buku->id))
        ->assertSessionHasErrors('buku');

    $this->assertDatabaseCount('peminjamen', 0);
    expect($buku->fresh()->stok)->toBe(0);
});

test('user tidak bisa meminjam buku yang sama dua kali saat masih dipinjam', function () {
    $user = User::factory()->create();
    $buku = Buku::factory()->create(['stok' => 2]);

    Peminjaman::query()->create([
        'user_id' => $user->id,
        'buku_id' => $buku->id,
        'tanggal_pinjam' => today(),
        'tenggat' => today()->addDays(14),
        'status' => 'dipinjam',
        'denda' => 0,
    ]);
    $buku->decrement('stok');

    $this->actingAs($user)
        ->post(route('loans.store', $buku))
        ->assertSessionHasErrors('buku');

    $this->assertDatabaseCount('peminjamen', 1);
    expect($buku->fresh()->stok)->toBe(1);
});

test('user dapat mengembalikan buku dan stok bertambah kembali', function () {
    $user = User::factory()->create();
    $buku = Buku::factory()->create(['stok' => 1]);
    $peminjaman = Peminjaman::query()->create([
        'user_id' => $user->id,
        'buku_id' => $buku->id,
        'tanggal_pinjam' => today(),
        'tenggat' => today()->addDays(14),
        'status' => 'dipinjam',
        'denda' => 0,
    ]);
    $buku->decrement('stok');

    $this->actingAs($user)
        ->post(route('loans.return', $peminjaman))
        ->assertRedirect()
        ->assertSessionHas('status');

    $peminjaman->refresh();

    expect($peminjaman->status)->toBe('dikembalikan');
    expect($peminjaman->tanggal_kembali?->toDateString())->toBe(today()->toDateString());
    expect($buku->fresh()->stok)->toBe(1);
});

test('user lain tidak bisa mengembalikan peminjaman milik orang lain', function () {
    $pemilik = User::factory()->create();
    $userLain = User::factory()->create();
    $buku = Buku::factory()->create(['stok' => 1]);
    $peminjaman = Peminjaman::query()->create([
        'user_id' => $pemilik->id,
        'buku_id' => $buku->id,
        'tanggal_pinjam' => today(),
        'tenggat' => today()->addDays(14),
        'status' => 'dipinjam',
        'denda' => 0,
    ]);

    $this->actingAs($userLain)
        ->post(route('loans.return', $peminjaman))
        ->assertForbidden();

    expect($peminjaman->fresh()->status)->toBe('dipinjam');
});

test('halaman detail menampilkan tombol pinjam atau kembalikan sesuai status', function () {
    $user = User::factory()->create();
    $buku = Buku::factory()->create(['stok' => 1]);
    $this->actingAs($user);

    $this->get(route('books.show', $buku->id))
        ->assertOk()
        ->assertSee('Pinjam Buku')
        ->assertSee('Stok tersedia');

    $this->post(route('loans.store', $buku))->assertRedirect();

    $this->get(route('books.show', $buku->id))
        ->assertOk()
        ->assertSee('Kembalikan Buku')
        ->assertSee('Dipinjam');
});

test('halaman peminjaman saya menampilkan daftar pinjaman user', function () {
    $user = User::factory()->create();
    $buku = Buku::factory()->create(['judul' => 'Buku Pinjaman Saya']);
    Peminjaman::query()->create([
        'user_id' => $user->id,
        'buku_id' => $buku->id,
        'tanggal_pinjam' => today(),
        'tenggat' => today()->addDays(14),
        'status' => 'dipinjam',
        'denda' => 0,
    ]);

    $this->actingAs($user)
        ->get(route('loans.index'))
        ->assertOk()
        ->assertSee('Peminjaman Saya')
        ->assertSee('Buku Pinjaman Saya')
        ->assertSee('Kembalikan Buku');
});
