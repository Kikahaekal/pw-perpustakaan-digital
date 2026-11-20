@extends('layouts.admin')

@section('title', 'Admin Dashboard — NEVERMORE')

@section('content')
    @if (session('status'))
        <div class="mb-4 rounded-lg border border-emerald-400/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
            {{ session('status') }}
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

    {{-- ===== Uploads History / hasil pencarian ===== --}}
    <section class="mt-6 rounded-xl bg-[#0d1330] px-3 py-6 md:px-6">
        <div class="flex flex-col items-center">
            <svg viewBox="0 0 24 24" class="h-7 w-7" fill="none" stroke="#fff" stroke-width="1.7">
                <path d="M4 15v3a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-3" stroke-linecap="round"/>
                <path d="M12 16V4m0 0 4 4m-4-4L8 8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <h2 class="mt-1 text-lg font-bold tracking-wide md:text-xl">
                {{ $q !== '' ? 'Hasil Pencarian' : 'Uploads History' }}
            </h2>
            <p class="mt-1 text-center text-[11px] text-slate-400">
                @if ($q !== '')
                    {{ $books->count() }} buku cocok dengan “{{ $q }}” ·
                    <a href="{{ route('admin.dashboard') }}" class="text-[#7f9bff] hover:underline">Reset</a>
                @else
                    {{ $books->count() }} buku terbaru · klik kartu untuk melihat detail, tombol Edit untuk mengubah.
                @endif
            </p>
        </div>

        <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-3 md:gap-4 lg:grid-cols-5">
            @forelse ($books as $buku)
                @include('admin._book-card', ['buku' => $buku])
            @empty
                <p class="col-span-full rounded-lg bg-white/5 p-8 text-center text-sm text-slate-300">
                    {{ $q !== '' ? 'Tidak ada buku yang cocok dengan pencarian.' : 'Belum ada buku. Tambahkan buku baru lewat tombol di atas.' }}
                </p>
            @endforelse
        </div>
    </section>

    @if ($errors->any())
        <script id="oldInput" type="application/json">{!! json_encode(old(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
    @endif
@endsection
