@extends('layouts.nevermore')

@section('title', 'Peminjaman Saya — NEVERMORE')

@section('content')
    @php
        $aktif = $peminjamans->where('status', 'dipinjam');
        $selesai = $peminjamans->where('status', 'dikembalikan');
    @endphp

    <section class="bg-[#0d1330] px-4 py-8 md:px-8">
        <div class="mx-auto max-w-6xl">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold md:text-3xl">Peminjaman Saya</h1>
                    <p class="mt-1 text-xs text-slate-400 md:text-sm">
                        Buku yang sedang dipinjam dan riwayat peminjaman Anda.
                    </p>
                </div>
                <a href="{{ route('dashboard') }}" class="rounded bg-[#2f4bd7] px-4 py-2 text-xs font-bold hover:bg-[#2540bd]">
                    Cari Buku Lain
                </a>
            </div>

            @if (session('status'))
                <div class="mt-5 rounded border border-emerald-400/40 bg-emerald-500/10 px-4 py-2 text-sm text-emerald-200">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mt-5 rounded border border-red-400/40 bg-red-500/10 px-4 py-2 text-sm text-red-200">
                    {{ $errors->first() }}
                </div>
            @endif

            <h2 class="mt-8 text-lg font-bold">Sedang Dipinjam ({{ $aktif->count() }})</h2>
            <div class="mt-3 overflow-x-auto rounded-lg ring-1 ring-white/10">
                <table class="w-full min-w-[720px] border-collapse text-left text-xs md:text-sm">
                    <thead class="bg-[#121a3d] text-[10px] uppercase tracking-wider text-slate-400">
                        <tr>
                            <th class="border-b border-white/10 px-3 py-3 font-semibold">Buku</th>
                            <th class="border-b border-white/10 px-3 py-3 font-semibold">Tanggal Pinjam</th>
                            <th class="border-b border-white/10 px-3 py-3 font-semibold">Tenggat</th>
                            <th class="border-b border-white/10 px-3 py-3 font-semibold">Status</th>
                            <th class="border-b border-white/10 px-3 py-3 text-center font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 bg-[#0a1029]">
                        @forelse ($aktif as $peminjaman)
                            <tr class="hover:bg-white/[0.03]">
                                <td class="px-3 py-3">
                                    <a href="{{ route('books.show', $peminjaman->buku_id) }}" class="flex items-center gap-3 hover:text-[#a9bbff]">
                                        <span class="flex h-14 w-10 shrink-0 items-center justify-center overflow-hidden rounded bg-[#d9d9d9] ring-1 ring-white/10">
                                            @if ($peminjaman->buku?->cover_url)
                                                <img src="{{ $peminjaman->buku->cover_url }}" alt="" class="h-full w-full object-cover">
                                            @else
                                                <span class="flex h-full w-full items-center justify-center bg-gradient-to-br {{ $peminjaman->buku?->cover ?? 'from-slate-700 via-slate-900 to-black' }} text-[10px] font-black text-white/90">
                                                    {{ $peminjaman->buku?->inisial }}
                                                </span>
                                            @endif
                                        </span>
                                        <span>
                                            <span class="block font-semibold text-white">{{ $peminjaman->buku?->judul ?? 'Buku dihapus' }}</span>
                                            <span class="block text-[10px] text-slate-400">{{ $peminjaman->buku?->pengarang }}</span>
                                        </span>
                                    </a>
                                </td>
                                <td class="whitespace-nowrap px-3 py-3 text-slate-300">{{ $peminjaman->tanggal_pinjam?->format('d/m/Y') }}</td>
                                <td class="whitespace-nowrap px-3 py-3 text-slate-300">{{ $peminjaman->tenggat?->format('d/m/Y') }}</td>
                                <td class="px-3 py-3">
                                    <span class="rounded bg-amber-500/15 px-2 py-0.5 text-[10px] font-semibold text-amber-200 ring-1 ring-amber-400/30">Dipinjam</span>
                                </td>
                                <td class="px-3 py-3 text-center">
                                    <form method="POST" action="{{ route('loans.return', $peminjaman) }}">
                                        @csrf
                                        <button class="rounded bg-[#f4b400] px-3 py-1.5 text-[11px] font-bold text-slate-900 hover:bg-[#e0a400]">
                                            Kembalikan Buku
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-400">
                                    Belum ada buku yang sedang dipinjam.
                                    <a href="{{ route('dashboard') }}" class="text-[#7f9bff] hover:underline">Cari buku sekarang</a>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <h2 class="mt-8 text-lg font-bold">Riwayat ({{ $selesai->count() }})</h2>
            <div class="mt-3 overflow-x-auto rounded-lg ring-1 ring-white/10">
                <table class="w-full min-w-[640px] border-collapse text-left text-xs md:text-sm">
                    <thead class="bg-[#121a3d] text-[10px] uppercase tracking-wider text-slate-400">
                        <tr>
                            <th class="border-b border-white/10 px-3 py-3 font-semibold">Buku</th>
                            <th class="border-b border-white/10 px-3 py-3 font-semibold">Tanggal Pinjam</th>
                            <th class="border-b border-white/10 px-3 py-3 font-semibold">Tanggal Kembali</th>
                            <th class="border-b border-white/10 px-3 py-3 font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 bg-[#0a1029]">
                        @forelse ($selesai as $peminjaman)
                            <tr class="hover:bg-white/[0.03]">
                                <td class="px-3 py-3">
                                    <a href="{{ route('books.show', $peminjaman->buku_id) }}" class="font-semibold text-white hover:text-[#a9bbff]">
                                        {{ $peminjaman->buku?->judul ?? 'Buku dihapus' }}
                                    </a>
                                    <span class="block text-[10px] text-slate-400">{{ $peminjaman->buku?->pengarang }}</span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-3 text-slate-300">{{ $peminjaman->tanggal_pinjam?->format('d/m/Y') }}</td>
                                <td class="whitespace-nowrap px-3 py-3 text-slate-300">{{ $peminjaman->tanggal_kembali?->format('d/m/Y') ?? '-' }}</td>
                                <td class="px-3 py-3">
                                    <span class="rounded bg-emerald-500/15 px-2 py-0.5 text-[10px] font-semibold text-emerald-200 ring-1 ring-emerald-400/30">Dikembalikan</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-sm text-slate-400">Belum ada riwayat peminjaman.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
