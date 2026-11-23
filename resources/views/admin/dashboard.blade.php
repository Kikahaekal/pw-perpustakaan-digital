@extends('layouts.admin')

@section('title', 'Admin Dashboard — NEVERMORE')

@section('content')
    @if (session('status'))
        <div class="mb-4 rounded-lg border border-emerald-400/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
            {{ session('status') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 rounded-lg border border-red-400/40 bg-red-500/10 px-4 py-3 text-sm text-red-200">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 rounded-lg border border-red-400/40 bg-red-500/10 px-4 py-3 text-sm text-red-200">
            <p class="font-semibold">Buku gagal disimpan:</p>
            <ul class="mt-1 list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ===== Statistik + grafik ===== --}}
    <div class="grid gap-4 lg:grid-cols-12">
        <div class="grid grid-cols-2 gap-3 lg:col-span-7">
            <x-admin.stat-card label="Total Titles" :value="number_format($stats['titles'], 0, ',', '.')" caption="Koleksi buku" />
            <x-admin.stat-card label="Active Readers" :value="number_format($stats['readers'], 0, ',', '.')" caption="Peminjam terdaftar" />
            <x-admin.stat-card label="Peminjaman Bulanan" :value="number_format($stats['monthly_loans'], 0, ',', '.')" caption="Bulan ini" />
            <x-admin.stat-card label="Flagged Content" :value="number_format($stats['flagged'], 0, ',', '.')" caption="Stok habis" />
        </div>

        <section class="rounded-xl border border-white/10 bg-[#05070f] p-3 lg:col-span-5">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div>
                    <h2 class="text-[11px] font-semibold tracking-wide text-slate-200">E-library Admin Page</h2>
                    <p class="text-[8px] uppercase tracking-[0.15em] text-slate-500">Statistik aktivitas perpustakaan</p>
                </div>
                <form method="GET" action="{{ route('admin.dashboard') }}">
                    @if ($q !== '')
                        <input type="hidden" name="q" value="{{ $q }}">
                    @endif
                    <select name="range" onchange="this.form.submit()"
                        class="rounded bg-[#1b2342] px-2 py-1 text-[10px] font-semibold text-slate-200 ring-1 ring-white/10 focus:outline-none">
                        <option value="6" @selected($range === 6)>Last 6 Months</option>
                        <option value="12" @selected($range === 12)>Last 12 Months</option>
                    </select>
                </form>
            </div>

            <div class="mt-3 grid gap-3 sm:grid-cols-2">
                <div class="rounded-lg bg-[#080d1c] p-2 ring-1 ring-white/5">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-[9px] font-semibold text-slate-300">Monthly Active Users</p>
                        <span class="rounded bg-[#1b2342] px-1.5 py-0.5 text-[8px] text-slate-400">Last {{ $range }} Months</span>
                    </div>
                    <x-admin.line-chart :labels="$activeUsersChart['labels']" :values="$activeUsersChart['values']" class="mt-1" />
                </div>

                <div class="rounded-lg bg-[#080d1c] p-2 ring-1 ring-white/5">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-[9px] font-semibold text-slate-300">Books Borrowed Monthly</p>
                        <span class="rounded bg-[#1b2342] px-1.5 py-0.5 text-[8px] text-slate-400">Last {{ $range }} Months</span>
                    </div>
                    <x-admin.bar-chart :labels="$borrowedChart['labels']" :values="$borrowedChart['values']" class="mt-1" />
                </div>
            </div>
        </section>
    </div>

    {{-- ===== Uploads History / hasil pencarian (tabel manajemen) ===== --}}
    <section class="mt-6 rounded-xl bg-[#0d1330] px-3 py-5 md:px-6 md:py-6">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold tracking-wide md:text-xl">
                    {{ $q !== '' ? 'Hasil Pencarian' : 'Uploads History' }}
                </h2>
                <p class="mt-1 text-[11px] text-slate-400 md:text-xs">
                    @if ($q !== '')
                        {{ $books->count() }} buku cocok dengan “{{ $q }}” ·
                        <a href="{{ route('admin.dashboard') }}" class="text-[#7f9bff] hover:underline">Reset</a>
                    @else
                        Kelola data buku: tambah, edit, lihat detail, atau hapus.
                    @endif
                </p>
            </div>
            <button type="button" data-open-add
                class="flex items-center gap-1.5 rounded bg-[#22c55e] px-4 py-2 text-sm font-semibold text-white shadow hover:bg-[#16a34a]"
                title="Tambah buku baru">
                <span class="text-base leading-none">+</span> Tambah Buku
            </button>
        </div>

        <div class="mt-4 overflow-x-auto rounded-lg ring-1 ring-white/10">
            <table class="w-full min-w-[820px] border-collapse text-left text-xs md:text-sm">
                <thead class="bg-[#121a3d] text-[10px] uppercase tracking-wider text-slate-400 md:text-[11px]">
                    <tr>
                        <th class="w-14 border-b border-white/10 px-3 py-3 font-semibold">No</th>
                        <th class="w-20 border-b border-white/10 px-3 py-3 font-semibold">Cover</th>
                        <th class="border-b border-white/10 px-3 py-3 font-semibold">Judul Buku</th>
                        <th class="border-b border-white/10 px-3 py-3 font-semibold">Author / Penulis</th>
                        <th class="border-b border-white/10 px-3 py-3 font-semibold">Tags / Genre</th>
                        <th class="w-32 border-b border-white/10 px-3 py-3 font-semibold">Tanggal Upload</th>
                        <th class="w-32 border-b border-white/10 px-3 py-3 text-center font-semibold">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 bg-[#0a1029]">
                    @forelse ($books as $index => $buku)
                        <tr class="align-middle transition hover:bg-white/[0.03]">
                            <td class="px-3 py-3 text-slate-400">{{ $index + 1 }}</td>

                            <td class="px-3 py-3">
                                <div class="flex h-14 w-10 items-center justify-center overflow-hidden rounded bg-[#d9d9d9] ring-1 ring-white/10">
                                    @if ($buku->cover_url)
                                        <img src="{{ $buku->cover_url }}" alt="Cover {{ $buku->judul }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center bg-gradient-to-br {{ $buku->cover ?? 'from-slate-700 via-slate-900 to-black' }}">
                                            <span class="text-[10px] font-black text-white/90">{{ $buku->inisial }}</span>
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <td class="max-w-64 px-3 py-3">
                                <p class="font-semibold text-white">{{ $buku->judul }}</p>
                                @if ($buku->seksiLabel())
                                    <p class="mt-0.5 text-[10px] text-slate-400">{{ $buku->seksiLabel() }}</p>
                                @endif
                            </td>

                            <td class="px-3 py-3 text-slate-300">{{ $buku->pengarang }}</td>

                            <td class="px-3 py-3">
                                <div class="flex max-w-56 flex-wrap gap-1">
                                    @forelse ($buku->kategoris as $kategori)
                                        <span class="rounded bg-[#2f4bd7]/25 px-2 py-0.5 text-[10px] font-semibold text-[#a9bbff] ring-1 ring-[#2f4bd7]/40">
                                            {{ $kategori->nama_kategori }}
                                        </span>
                                    @empty
                                        <span class="text-[10px] text-slate-500">-</span>
                                    @endforelse
                                </div>
                            </td>

                            <td class="whitespace-nowrap px-3 py-3 text-slate-300">
                                {{ $buku->created_at?->format('d/m/Y') ?? '-' }}
                            </td>

                            <td class="px-3 py-3">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button"
                                        class="edit-book flex h-8 w-8 items-center justify-center rounded bg-[#2f4bd7] text-white shadow hover:bg-[#2540bd]"
                                        data-book="{{ json_encode([
                                            'id' => $buku->id,
                                            'judul' => $buku->judul,
                                            'pengarang' => $buku->pengarang,
                                            'tags' => $buku->kategoris->pluck('nama_kategori')->implode(', '),
                                            'seksi' => $buku->seksi,
                                            'penerbit' => $buku->penerbit,
                                            'isbn' => $buku->isbn,
                                            'tahun_terbit' => $buku->tahun_terbit,
                                            'jumlah_halaman' => $buku->jumlah_halaman,
                                            'stok' => $buku->stok,
                                            'inisial' => $buku->inisial,
                                            'cover_image' => $buku->cover_image,
                                        ]) }}"
                                        title="Edit {{ $buku->judul }}">
                                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M4 20h4l10-10-4-4L4 16v4z" stroke-linejoin="round"/>
                                            <path d="M13.5 6.5l4 4" stroke-linecap="round"/>
                                        </svg>
                                    </button>

                                    <a href="{{ route('books.show', $buku->id) }}" target="_blank" rel="noopener"
                                        class="flex h-8 w-8 items-center justify-center rounded bg-[#f4b400] text-slate-900 shadow hover:bg-[#e0a400]"
                                        title="Lihat detail {{ $buku->judul }}">
                                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12z"/>
                                            <circle cx="12" cy="12" r="2.5"/>
                                        </svg>
                                    </a>

                                    <form method="POST" action="{{ route('admin.books.destroy', $buku) }}" class="inline"
                                        onsubmit="return confirm('Hapus buku ini? Tindakan ini tidak dapat dibatalkan.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="flex h-8 w-8 items-center justify-center rounded bg-[#dc2626] text-white shadow hover:bg-[#b91c1c]"
                                            title="Hapus {{ $buku->judul }}">
                                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M4 7h16" stroke-linecap="round"/>
                                                <path d="M9 7V5h6v2" stroke-linejoin="round"/>
                                                <path d="M6 7l1 13h10l1-13" stroke-linejoin="round"/>
                                                <path d="M10 11v5M14 11v5" stroke-linecap="round"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-sm text-slate-300">
                                {{ $q !== '' ? 'Tidak ada buku yang cocok dengan pencarian.' : 'Belum ada buku. Tambahkan buku baru lewat tombol Tambah Buku.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @if ($errors->any())
        <script id="oldInput" type="application/json">{!! json_encode(old(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
    @endif
@endsection
