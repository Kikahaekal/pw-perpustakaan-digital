<?php

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('tamu diarahkan ke login saat membuka dashboard admin', function () {
    $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
});

test('user biasa tidak boleh membuka dashboard admin', function () {
    $user = User::factory()->create(['role' => 'user']);
    $this->actingAs($user);

    $this->get(route('admin.dashboard'))->assertForbidden();
    $this->post(route('admin.books.store'), [
        'judul' => 'Buku Ilegal',
        'pengarang' => 'Penulis',
    ])->assertForbidden();
});

test('admin login diarahkan ke dashboard admin', function () {
    User::factory()->create([
        'email' => 'admin-login@test.com',
        'password' => 'password123',
        'role' => 'admin',
    ]);

    $this->post(route('login'), [
        'email' => 'admin-login@test.com',
        'password' => 'password123',
    ])->assertRedirect(route('admin.dashboard'));
});

test('admin melihat statistik, grafik, dan riwayat unggahan', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $buku = Buku::factory()->create(['judul' => 'Buku Grafik Admin', 'stok' => 0]);
    Peminjaman::query()->create([
        'user_id' => $admin->id,
        'buku_id' => $buku->id,
        'tanggal_pinjam' => today(),
        'tenggat' => today()->addDays(7),
        'tanggal_kembali' => today(),
        'status' => 'dikembalikan',
        'denda' => 0,
    ]);

    $this->actingAs($admin);

    $this->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Total Titles')
        ->assertSee('Active Readers')
        ->assertSee('Peminjaman Bulanan')
        ->assertSee('Flagged Content')
        ->assertSee('Monthly Active Users')
        ->assertSee('Books Borrowed Monthly')
        ->assertSee('Uploads History')
        ->assertSee('Judul Buku')
        ->assertSee('Author / Penulis')
        ->assertSee('Tags / Genre')
        ->assertSee('Tanggal Upload')
        ->assertSee('Action')
        ->assertSee('Buku Grafik Admin')
        ->assertSee('Add New Book');
});

test('admin mencari buku lewat form pencarian yang sama seperti user', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    Buku::factory()->create(['judul' => 'Tales Of Horror', 'pengarang' => 'HP. Lovecraft']);
    Buku::factory()->create(['judul' => 'Buku Lain', 'pengarang' => 'Penulis Lain']);

    $this->actingAs($admin);

    $this->get(route('admin.dashboard', ['q' => 'Tales']))
        ->assertOk()
        ->assertSee('Hasil Pencarian')
        ->assertSee('Tales Of Horror')
        ->assertDontSee('Buku Lain');
});

test('admin bisa menambah buku baru lengkap dengan tag dan section', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $this->post(route('admin.books.store'), [
        'judul' => 'Buku Baru Admin',
        'pengarang' => 'Penulis Baru',
        'tags' => 'Horror, Misteri',
        'seksi' => 'popular_now',
        'penerbit' => 'Penerbit Baru',
        'tahun_terbit' => 2025,
        'stok' => 3,
    ])->assertRedirect(route('admin.dashboard'));

    $buku = Buku::query()->where('judul', 'Buku Baru Admin')->firstOrFail();

    expect($buku->pengarang)->toBe('Penulis Baru');
    expect($buku->seksi)->toBe('popular_now');
    expect($buku->stok)->toBe(3);
    expect($buku->inisial)->toBe('BBA');
    expect($buku->kategoris()->pluck('nama_kategori')->sort()->values()->all())->toBe(['Horror', 'Misteri']);
});

test('admin bisa mengubah buku dan mengunggah cover baru', function () {
    Storage::fake('public');

    $admin = User::factory()->create(['role' => 'admin']);
    $buku = Buku::factory()->create([
        'judul' => 'Judul Lama',
        'pengarang' => 'Pengarang Lama',
        'stok' => 2,
    ]);

    $this->actingAs($admin);

    $this->put(route('admin.books.update', $buku), [
        'judul' => 'Judul Baru',
        'pengarang' => 'Pengarang Baru',
        'tags' => 'Fantasi',
        'seksi' => 'highly_requested',
        'cover_image' => UploadedFile::fake()->image('cover.jpg'),
    ])->assertRedirect(route('admin.dashboard'));

    $buku->refresh();

    expect($buku->judul)->toBe('Judul Baru');
    expect($buku->seksi)->toBe('highly_requested');
    expect($buku->cover_image)->not->toBeNull();

    Storage::disk('public')->assertExists($buku->cover_image);

    expect($buku->kategoris()->pluck('nama_kategori')->all())->toBe(['Fantasi']);
    expect(Kategori::query()->where('nama_kategori', 'Fantasi')->exists())->toBeTrue();
});

test('admin bisa menghapus buku beserta cover dan kategorinya', function () {
    Storage::fake('public');

    $admin = User::factory()->create(['role' => 'admin']);
    $kategori = Kategori::factory()->create();
    $buku = Buku::factory()->create(['cover_image' => 'covers/hapus.jpg']);
    $buku->kategoris()->attach($kategori);
    Storage::disk('public')->put('covers/hapus.jpg', 'dummy');

    $this->actingAs($admin);

    $this->delete(route('admin.books.destroy', $buku))
        ->assertRedirect(route('admin.dashboard'))
        ->assertSessionHas('status');

    $this->assertDatabaseMissing('bukus', ['id' => $buku->id]);
    $this->assertDatabaseMissing('buku_kategoris', ['buku_id' => $buku->id]);
    Storage::disk('public')->assertMissing('covers/hapus.jpg');
});

test('buku dengan riwayat peminjaman tidak bisa dihapus', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $buku = Buku::factory()->create();
    Peminjaman::query()->create([
        'user_id' => $admin->id,
        'buku_id' => $buku->id,
        'tanggal_pinjam' => today(),
        'tenggat' => today()->addDays(7),
        'status' => 'dipinjam',
        'denda' => 0,
    ]);

    $this->actingAs($admin);

    $this->delete(route('admin.books.destroy', $buku))
        ->assertRedirect(route('admin.dashboard'))
        ->assertSessionHas('error');

    $this->assertDatabaseHas('bukus', ['id' => $buku->id]);
});

test('user biasa tidak bisa menghapus buku lewat route admin', function () {
    $user = User::factory()->create(['role' => 'user']);
    $buku = Buku::factory()->create();

    $this->actingAs($user);

    $this->delete(route('admin.books.destroy', $buku))->assertForbidden();

    $this->assertDatabaseHas('bukus', ['id' => $buku->id]);
});
