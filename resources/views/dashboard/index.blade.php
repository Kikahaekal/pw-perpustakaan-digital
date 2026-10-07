@extends('layouts.nevermore')

@section('title', 'NEVERMORE — Home')

@section('content')
    @if ($q || $activeTag || $activeAuthor || $onlyFavorites)
        {{-- ===== Mode hasil pencarian/filter ===== --}}
        <section class="bg-[#0d1330] px-4 py-6 md:px-8">
            <h2 class="text-center text-xl font-bold">
                @if ($onlyFavorites) ❤️ Favorites ({{ $books->count() }})
                @else Hasil pencarian ({{ $books->count() }})
                @endif
            </h2>
            <p class="mt-1 text-center text-xs text-slate-300">
                @if ($q) Kata kunci: “{{ $q }}” @endif
                @if ($activeTag) • Tag: {{ $activeTag }} @endif
                @if ($activeAuthor) • Author: {{ $activeAuthor }} @endif
            </p>
            <div class="mx-auto mt-5 grid max-w-6xl grid-cols-2 gap-4 md:grid-cols-5">
                @forelse ($books as $book)
                    @include('dashboard._popular-card', ['book' => $book])
                @empty
                    <p class="col-span-full rounded bg-white/10 p-6 text-center text-sm">Tidak ada buku yang cocok.</p>
                @endforelse
            </div>
            <p class="mt-5 text-center"><a href="{{ route('dashboard') }}" class="text-sm text-red-400 hover:underline">Reset filter</a></p>
        </section>
    @else
        {{-- ===== Popular Now (sesuai Figma: 5 kolom) ===== --}}
        <section class="bg-[#0d1330] px-4 py-5 md:px-8">
            <x-section-title>Popular Now</x-section-title>
            <div class="mx-auto mt-4 grid max-w-6xl grid-cols-2 gap-4 md:grid-cols-5">
                @foreach ($popular as $book)
                    @include('dashboard._popular-card', ['book' => $book])
                @endforeach
            </div>
        </section>

        <div class="h-4 bg-black"></div>

        {{-- ===== New Uploads (sesuai Figma: 5 kolom x 2 baris) ===== --}}
        <section class="bg-[#0d1330] px-4 py-5 md:px-8">
            <x-section-title>New Uploads</x-section-title>
            <div class="mx-auto mt-4 grid max-w-6xl grid-cols-2 gap-4 md:grid-cols-5">
                @foreach ($newUploads as $book)
                    @include('dashboard._new-card', ['book' => $book])
                @endforeach
            </div>
        </section>

        <div class="h-4 bg-black"></div>

        {{-- ===== Random (menu sidebar) ===== --}}
        <section id="random" class="bg-[#0d1330] px-4 py-5 md:px-8">
            <x-section-title>Random</x-section-title>
            <div class="mx-auto mt-4 grid max-w-6xl grid-cols-2 gap-4 md:grid-cols-5">
                @foreach ($random as $book)
                    @include('dashboard._popular-card', ['book' => $book])
                @endforeach
            </div>
            <p class="mt-4 text-center"><a href="{{ route('dashboard') }}" class="rounded bg-[#2f4bd7] px-4 py-2 text-xs font-bold">Acak lagi</a></p>
        </section>

        <div class="h-4 bg-black"></div>

        {{-- ===== Tags & Author (menu sidebar) ===== --}}
        <section id="tags" class="bg-[#0d1330] px-4 py-5 md:px-8">
            <x-section-title>Tags</x-section-title>
            <div class="mx-auto mt-4 flex max-w-4xl flex-wrap justify-center gap-2">
                @foreach ($tags as $t)
                    <a href="{{ route('dashboard', ['tag' => $t]) }}" class="rounded bg-[#2f4bd7] px-3 py-1.5 text-xs font-bold hover:bg-[#2540bd]">{{ $t }}</a>
                @endforeach
            </div>
        </section>

        <section id="author" class="bg-[#0d1330] px-4 py-5 md:px-8">
            <x-section-title>Author</x-section-title>
            <div class="mx-auto mt-4 flex max-w-4xl flex-wrap justify-center gap-2">
                @foreach ($authors as $a)
                    <a href="{{ route('dashboard', ['author' => $a]) }}" class="rounded bg-white/10 px-3 py-1.5 text-xs font-bold hover:bg-white/20">{{ $a }}</a>
                @endforeach
            </div>
        </section>

        {{-- ===== Info (menu sidebar) ===== --}}
        <section id="info" class="bg-[#0d1330] px-4 py-6 md:px-8">
            <x-section-title>Info</x-section-title>
            <p class="mx-auto mt-3 max-w-2xl text-center text-xs leading-relaxed text-slate-300">
                NEVERMORE — Perpustakaan Digital kelompok Pemrograman Web.
                Progres tahap 1: login, register, reset password, katalog (Popular Now, New Uploads, Random),
                detail buku, filter tags/author, dan favorit (sesi). Pinjam & kelola buku tahap 2.
            </p>
        </section>
    @endif
@endsection
