<?php

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can manage books and categories with their relationship', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $kategori = $this->postJson(route('api.kategoris.store'), [
        'nama_kategori' => 'Fiksi',
    ])->assertCreated()->json();

    $buku = $this->postJson(route('api.bukus.store'), [
        'judul' => 'Buku Uji',
        'pengarang' => 'Penulis Uji',
        'penerbit' => 'Penerbit Uji',
        'tahun_terbit' => 2024,
        'stok' => 4,
        'kategori_ids' => [$kategori['id']],
    ])->assertCreated()
        ->assertJsonPath('kategoris.0.id', $kategori['id'])
        ->json();

    $this->patchJson(route('api.bukus.update', $buku['id']), [
        'judul' => 'Buku Diperbarui',
    ])->assertOk()->assertJsonPath('judul', 'Buku Diperbarui');

    $this->putJson(route('api.kategoris.update', $kategori['id']), [
        'nama_kategori' => 'Fiksi Ilmiah',
    ])->assertOk()->assertJsonPath('nama_kategori', 'Fiksi Ilmiah');

    $this->deleteJson(route('api.bukus.destroy', $buku['id']))->assertStatus(204);
    $this->deleteJson(route('api.kategoris.destroy', $kategori['id']))->assertStatus(204);

    $this->assertDatabaseMissing('bukus', ['id' => $buku['id']]);
    $this->assertDatabaseMissing('kategoris', ['id' => $kategori['id']]);
});

test('a user can borrow and return a book and the stock stays synchronized', function () {
    $user = User::factory()->create();
    $buku = Buku::factory()->create(['stok' => 1]);
    $this->actingAs($user);

    $peminjaman = $this->postJson(route('api.peminjamans.store'), [
        'buku_id' => $buku->id,
        'tenggat' => today()->addDays(7)->toDateString(),
    ])->assertCreated()
        ->assertJsonPath('status', 'dipinjam')
        ->assertJsonPath('user.id', $user->id)
        ->assertJsonPath('buku.id', $buku->id)
        ->json();

    $this->assertDatabaseHas('bukus', ['id' => $buku->id, 'stok' => 0]);
    $this->assertDatabaseHas('peminjamen', [
        'id' => $peminjaman['id'],
        'tanggal_kembali' => null,
        'status' => 'dipinjam',
    ]);

    $this->patchJson(route('api.peminjamans.update', $peminjaman['id']), [
        'status' => 'dikembalikan',
    ])->assertOk()->assertJsonPath('status', 'dikembalikan');

    $this->assertDatabaseHas('bukus', ['id' => $buku->id, 'stok' => 1]);
    $this->assertDatabaseHas('peminjamen', [
        'id' => $peminjaman['id'],
        'status' => 'dikembalikan',
    ]);
    expect(Peminjaman::query()->findOrFail($peminjaman['id'])->tanggal_kembali->toDateString())
        ->toBe(today()->toDateString());
});

test('borrowing fails without available stock and users cannot manage catalog records', function () {
    $user = User::factory()->create();
    $buku = Buku::factory()->create(['stok' => 0]);
    $kategori = Kategori::factory()->create();
    $this->actingAs($user);

    $this->getJson(route('api.bukus.index'))->assertOk();
    $this->getJson(route('api.bukus.show', $buku))->assertOk();
    $this->getJson(route('api.kategoris.index'))->assertOk();
    $this->getJson(route('api.kategoris.show', $kategori))->assertOk();

    $this->postJson(route('api.peminjamans.store'), [
        'buku_id' => $buku->id,
        'tenggat' => today()->addDays(7)->toDateString(),
    ])->assertUnprocessable();

    $this->postJson(route('api.bukus.store'), [
        'judul' => 'Buku Ilegal',
        'pengarang' => 'Penulis',
        'penerbit' => 'Penerbit',
        'tahun_terbit' => 2024,
        'stok' => 1,
    ])->assertForbidden();

    $this->assertDatabaseCount('peminjamen', 0);
    $this->assertDatabaseHas('bukus', ['id' => $buku->id, 'stok' => 0]);
});

test('a member can cancel an active loan and restore book stock', function () {
    $user = User::factory()->create();
    $buku = Buku::factory()->create(['stok' => 1]);
    $peminjaman = Peminjaman::query()->create([
        'user_id' => $user->id,
        'buku_id' => $buku->id,
        'tanggal_pinjam' => today(),
        'tenggat' => today()->addDays(7),
        'status' => 'dipinjam',
    ]);
    $buku->decrement('stok');
    $this->actingAs($user);

    $this->deleteJson(route('api.peminjamans.destroy', $peminjaman))->assertStatus(204);

    $this->assertDatabaseMissing('peminjamen', ['id' => $peminjaman->id]);
    $this->assertDatabaseHas('bukus', ['id' => $buku->id, 'stok' => 1]);
});
