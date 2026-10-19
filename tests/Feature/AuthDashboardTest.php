<?php

use App\Models\Buku;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
});

test('halaman login tampil ala NEVERMORE', function () {
    $this->get(route('login'))->assertOk()->assertSee('NEVERMORE');
});

test('halaman register tampil 4 input sesuai figma', function () {
    $this->get(route('register'))->assertOk()
        ->assertSee('Confirm Password')
        ->assertSee('Email');
});

test('halaman lupa password tampil', function () {
    $this->get(route('password.request'))->assertOk()->assertSee('Send Reset Link');
});

test('tamu diarahkan ke login saat buka dashboard', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

test('user bisa register lalu melihat dashboard NEVERMORE', function () {
    $this->post(route('register'), [
        'fullname' => 'Anggota Test',
        'email' => 'anggota@test.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertRedirect(route('dashboard'));

    $this->assertAuthenticated();

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Popular Now')
        ->assertSee('New Uploads')
        ->assertSee('Random');
});

test('user bisa login dan logout', function () {
    User::factory()->create([
        'fullname' => 'User Login',
        'email' => 'login@test.com',
        'password' => 'password123',
    ]);

    $this->post(route('login'), [
        'email' => 'login@test.com',
        'password' => 'password123',
    ])->assertRedirect(route('dashboard'));

    $this->assertAuthenticated();

    $this->post(route('logout'))->assertRedirect(route('login'));
    $this->assertGuest();
});

test('dashboard menampilkan katalog dari database', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertSee(Buku::query()->orderByDesc('populer')->value('judul'));
});

test('dashboard bisa mencari dan memfilter tag', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $buku = Buku::query()->where('judul', 'Tales Of Horror')->firstOrFail();

    $this->get(route('dashboard', ['q' => 'Tales']))->assertOk()->assertSee($buku->judul);
    $this->get(route('dashboard', ['tag' => 'Horror']))->assertOk()->assertSee('Horror');
    $this->get(route('dashboard', ['author' => 'Lovecraft']))->assertOk()->assertSee($buku->judul);
});

test('halaman detail buku tampil ala figma', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $buku = Buku::query()->where('judul', 'Tales Of Horror')->firstOrFail();

    $this->get(route('books.show', $buku->id))
        ->assertOk()
        ->assertSee($buku->judul)
        ->assertSee('#')
        ->assertSee((string) $buku->id)
        ->assertSee('Favorite');
});

test('halaman detail buku yang tidak ada memberi 404', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->get(route('books.show', 999999))->assertNotFound();
});

test('favorit bisa di-toggle dan muncul di menu favorites', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $buku = Buku::query()->value('id');

    $this->post(route('books.favorite', $buku))->assertRedirect();
    $this->get(route('dashboard', ['favorites' => 1]))
        ->assertOk()
        ->assertSee(Buku::query()->whereKey($buku)->value('judul'));
});
