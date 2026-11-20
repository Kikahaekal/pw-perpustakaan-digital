# Naskah Penjelasan Kode — Perpustakaan Digital “NEVERMORE”

Dokumen ini menjelaskan isi setiap file penting pada proyek Laravel `pw-perpustakaan-digital`.

---

## A. Routing

### `routes/web.php`
File yang memetakan URL ke controller.
- `/` : jika user sudah login diarahkan ke `dashboard`, jika belum ke `login`.
- Middleware `guest` : halaman login, register, lupa password.
- Middleware `auth` : dashboard, detail buku, toggle favorit, logout.
- Prefix `api` (nama `api.`) : CRUD JSON untuk buku, kategori, peminjaman.

### `routes/console.php`
File bawaan Laravel untuk perintah Artisan custom. Pada proyek ini tidak ada perintah custom tambahan.

---

## B. Controller (`app/Http/Controllers`)

### `Controller.php`
Kelas abstrak dasar semua controller. Kosong, hanya sebagai parent.

### `AuthController.php`
Menangani autentikasi pengguna.
- `showLogin`, `showRegister`, `showForgot` : menampilkan view.
- `login` : validasi email/password, `Auth::attempt`, regenerate session.
- `register` : validasi dan buat user baru dengan role `user`, lalu login.
- `sendResetLink` : demo tahap 1, belum kirim email sungguhan.
- `logout` : logout, invalidate session, regenerate token CSRF.

### `DashboardController.php`
Menangani halaman katalog dan detail buku.
- `kategorisRelation` : eager load kategori + jumlah buku.
- `authorBookCounts` : hitung jumlah buku per pengarang.
- `present` : ubah model `Buku` menjadi array untuk view.
- `presentMany` : ubah banyak buku sekaligus.
- `index` : query buku dengan filter q/tag/author/favorites, siapkan popular/new/random/tags/authors.
- `show` : detail satu buku + new uploads.
- `toggleFavorite` : simpan id buku favorit di session.

### `BukuController.php`
API CRUD buku (JSON), admin only untuk create/update/delete.
- `index` : list buku + kategori, pagination.
- `store` : validasi, simpan buku, sync kategori dalam transaksi.
- `show` : detail buku + kategori.
- `update` : update data, sync kategori jika ada.
- `destroy` : tolak hapus jika buku sudah dipinjam (409).
- `rules` : aturan validasi buku.
- `authorizeAdmin` : cek role admin.

### `KategoriController.php`
API CRUD kategori.
- `index` : list kategori + jumlah buku, pagination.
- `store` : buat kategori (admin only, nama unik).
- `show` : detail kategori + buku.
- `update` : update nama (admin only).
- `destroy` : hapus kategori (admin only).
- `authorizeAdmin` : cek role admin.

### `PeminjamanController.php`
API peminjaman buku.
- `index` : list peminjaman; user biasa hanya melihat miliknya, admin melihat semua.
- `store` : lock baris buku, cek stok, kurangi stok, buat record peminjaman.
- `show` : detail peminjaman (pemilik/admin).
- `update` : kembalikan buku, set tanggal kembali, tambah stok.
- `destroy` : batalkan peminjaman aktif dan kembalikan stok.
- `authorizeOwnerOrAdmin` : cek pemilik atau admin.

---

## C. Model (`app/Models`)

### `User.php`
Model user autentikasi.
- Fillable: `fullname`, `email`, `password`, `role`.
- Hidden: `password`, `remember_token`.
- Casts: password hashed, email_verified_at datetime.
- Relasi: `hasMany Peminjaman`.

### `Buku.php`
Model buku.
- Fillable data buku.
- Relasi: `belongsToMany Kategori` (pivot `buku_kategoris`), `hasMany Peminjaman`.

### `Kategori.php`
Model kategori.
- Fillable: `nama_kategori`.
- Relasi: `belongsToMany Buku`.

### `Peminjaman.php`
Model peminjaman.
- Table: `peminjamen`.
- Fillable: user_id, buku_id, tanggal_pinjam, tenggat, tanggal_kembali, status, denda.
- Casts: tanggal ke date, denda integer.
- Relasi: `belongsTo User`, `belongsTo Buku`.

---

## D. Database

### Migrations (`database/migrations`)
- `0001_01_01_000000_create_users_table.php` : tabel users, password_reset_tokens, sessions.
- `0001_01_01_000001_create_cache_table.php` : tabel cache & cache_locks.
- `0001_01_01_000002_create_jobs_table.php` : tabel jobs, job_batches, failed_jobs.
- `2026_09_21_015023_create_bukus_table.php` : tabel bukus dasar.
- `2026_09_21_015127_create_peminjamen_table.php` : tabel peminjamen.
- `2026_09_21_021309_create_kategoris_table.php` : tabel kategoris.
- `2026_09_21_021348_create_buku__kategoris_table.php` : pivot buku-kategori.
- `2026_10_19_052022_add_katalog_fields_to_bukus_table.php` : tambah isbn, jumlah_halaman, inisial, cover, populer, jumlah_favorit.
- `2026_10_26_023215_make_tanggal_kembali_nullable_on_peminjamen_table.php` : tanggal_kembali boleh null.

### Seeders (`database/seeders`)
- `KategoriSeeder.php` : buat 13 kategori contoh.
- `BukuSeeder.php` : buat 10 buku contoh NEVERMORE + relasi kategori.
- `DatabaseSeeder.php` : panggil seeder kategori & buku, buat user & admin demo.

### Factories (`database/factories`)
- `BukuFactory.php` : data palsu buku (Faker).
- `KategoriFactory.php` : data palsu kategori.
- `UserFactory.php` : data palsu user.

---

## E. Views Blade (`resources/views`)

### Layouts
- `layouts/app.blade.php` : layout umum (Tailwind via Vite/CDN).
- `layouts/auth.blade.php` : layout login/register (logo NEVERMORE).
- `layouts/nevermore.blade.php` : layout dashboard (header search + menu + sidebar).

### Auth
- `auth/login.blade.php` : form login.
- `auth/register.blade.php` : form register (4 input).
- `auth/forgot.blade.php` : form lupa password.

### Dashboard
- `dashboard/index.blade.php` : katalog atau hasil filter.
- `dashboard/show.blade.php` : detail buku + favorit.
- `dashboard/_popular-card.blade.php` : kartu popular.
- `dashboard/_new-card.blade.php` : kartu new uploads.
- `dashboard/_section-title.blade.php` : judul section.

### Components
- `components/section-title.blade.php` : komponen judul section.

### Lainnya
- `welcome.blade.php` : halaman default Laravel.

---

## F. Frontend Build

- `resources/css/app.css` : import Tailwind v4 + sumber scan.
- `resources/js/app.js` : kosong (interaksi menu ada inline script).
- `vite.config.js` : entry CSS/JS, font Instrument Sans, plugin Tailwind.
- `package.json` : dev dependencies Tailwind/Vite.

---

## G. Config Penting

- `.env` : DB MySQL `db_perpustakaan`, session/cache/queue pakai database, mail log.
- `config/app.php`, `auth.php`, `database.php`, `session.php`, dll. : konfigurasi standar Laravel.

---

## H. Tests (`tests`)

- `tests/Pest.php` : konfigurasi Pest.
- `tests/TestCase.php` : base test case.
- `tests/Feature/AuthDashboardTest.php` : test login/register/filter/detail/favorit.
- `tests/Feature/LibraryBackendTest.php` : test API buku/kategori/peminjaman.
- `tests/Unit/ExampleTest.php` : contoh unit test.

Menjalankan test: `php artisan test --compact` atau `vendor/bin/pest`.
