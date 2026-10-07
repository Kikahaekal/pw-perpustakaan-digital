<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

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

test('dashboard bisa mencari dan memfilter tag', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->get(route('dashboard', ['q' => 'Horror']))->assertOk()->assertSee('Tales Of Horror');
    $this->get(route('dashboard', ['tag' => 'Horror']))->assertOk()->assertSee('Horror');
});

test('halaman detail buku tampil ala figma', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->get(route('books.show', 666))
        ->assertOk()
        ->assertSee('Tales Of Horror')
        ->assertSee('#')
        ->assertSee('666')
        ->assertSee('Favorite');
});

test('favorit bisa di-toggle dan muncul di menu favorites', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->post(route('books.favorite', 666))->assertRedirect();
    $this->get(route('dashboard', ['favorites' => 1]))->assertOk()->assertSee('Tales Of Horror');
});
