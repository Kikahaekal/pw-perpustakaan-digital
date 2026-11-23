@extends('layouts.nevermore')

@section('title', $book['judul'].' — NEVERMORE')

@section('content')
    {{-- ===== Detail buku (sesuai Figma) ===== --}}
    <section class="bg-[#0d1330] px-4 py-8 md:px-12">
        @if (session('status'))
            <div class="mx-auto mb-5 max-w-6xl rounded border border-emerald-400/40 bg-emerald-500/10 px-4 py-2 text-sm text-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mx-auto mb-5 max-w-6xl rounded border border-red-400/40 bg-red-500/10 px-4 py-2 text-sm text-red-200">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="mx-auto grid max-w-6xl gap-8 md:grid-cols-2">
            <div class="mx-auto w-full max-w-sm overflow-hidden rounded">
                <div class="relative flex aspect-[3/4] items-center justify-center bg-gradient-to-br {{ $book['cover'] }} p-6 text-center">
                    @if ($book['cover_url'] ?? null)
                        <img src="{{ $book['cover_url'] }}" alt="Cover {{ $book['judul'] }}" class="absolute inset-0 h-full w-full object-cover">
                    @else
                        <div class="relative">
                            <p class="text-5xl font-black text-white/90">{{ $book['inisial'] }}</p>
                            <p class="mt-2 text-lg font-bold text-white/85">{{ $book['judul'] }}</p>
                            <p class="text-sm text-white/70">{{ $book['author'] }}</p>
                        </div>
                    @endif
                </div>
            </div>
            <div class="md:pt-4">
                <h1 class="text-center text-2xl font-bold md:text-left md:text-3xl">
                    <span class="text-white/40">[{{ strtoupper($book['author']) }}]</span>
                    {{ $book['judul'] }}
                    <span class="text-white/40">[English]</span>
                </h1>
                <p class="mt-8 text-xl font-bold md:text-2xl"><span class="text-white/40">#</span>{{ $book['id'] }}</p>

                <p class="mt-4 text-base font-bold">Tags:
                    @foreach ($book['tags'] as $t)
                        <a href="{{ route('dashboard', ['tag' => $t['nama']]) }}" class="ml-1 rounded bg-[#2f4bd7] px-2.5 py-1 text-sm font-bold text-white">{{ $t['nama'] }}</a><span class="rounded-r bg-[#3a3a3a] px-2 py-1 text-sm">{{ $t['count'] }}</span>
                    @endforeach
                </p>
                <p class="mt-3 text-base font-bold">Author:
                    <a href="{{ route('dashboard', ['author' => $book['author']]) }}" class="rounded bg-[#2f4bd7] px-2.5 py-1 text-sm font-bold text-white">{{ $book['author'] }}</a><span class="rounded-r bg-[#3a3a3a] px-2 py-1 text-sm">{{ $book['author_books'] }}</span>
                </p>
                <p class="mt-3 text-base font-bold">Pages:
                    <span class="rounded bg-[#2f4bd7] px-2 py-1 text-sm font-bold text-white">{{ $book['halaman'] }}</span>
                </p>
                <p class="mt-6 text-base font-bold">Uploaded:
                    <span class="text-sm font-normal text-white/70">{{ $book['uploaded'] }}</span>
                    <span class="block text-sm font-normal text-white/50">(DD/MM/YYYY)</span>
                </p>

                <div class="mt-6 flex flex-wrap items-center gap-3">
                    @if ($activeLoan)
                        <form method="POST" action="{{ route('loans.return', $activeLoan) }}">
                            @csrf
                            <button class="rounded bg-[#f4b400] px-5 py-2 text-sm font-bold text-slate-900 hover:bg-[#e0a400]">
                                Kembalikan Buku
                            </button>
                        </form>
                        <span class="text-xs text-amber-200">
                            Dipinjam {{ $activeLoan->tanggal_pinjam?->format('d/m/Y') }} ·
                            Tenggat {{ $activeLoan->tenggat?->format('d/m/Y') }}
                        </span>
                    @elseif ($book['stok'] > 0)
                        <form method="POST" action="{{ route('loans.store', $book['id']) }}">
                            @csrf
                            <button class="rounded bg-[#2f4bd7] px-5 py-2 text-sm font-bold text-white hover:bg-[#2540bd]">
                                Pinjam Buku
                            </button>
                        </form>
                        <span class="text-xs text-emerald-300">Stok tersedia: {{ $book['stok'] }}</span>
                    @else
                        <button disabled class="cursor-not-allowed rounded bg-red-900 px-5 py-2 text-sm font-bold text-red-200">
                            Stok Buku Habis
                        </button>
                    @endif
                </div>

                <form method="POST" action="{{ route('books.favorite', $book['id']) }}" class="mt-10">
                    @csrf
                    <button class="bg-[#2f4bd7] px-5 py-2 text-lg font-bold hover:bg-[#2540bd]">
                        {{ ($book['is_favorit'] ?? false) ? '❤️' : '🤍' }} Favorite ({{ $book['favorit'] + (($book['is_favorit'] ?? false) ? 1 : 0) }})
                    </button>
                </form>
            </div>
        </div>
    </section>

    <div class="h-4 bg-black"></div>

    {{-- ===== New Uploads di bawah detail (sesuai Figma) ===== --}}
    <section class="bg-[#0d1330] px-4 py-5 md:px-8">
        <x-section-title>New Uploads</x-section-title>
        <div class="mx-auto mt-4 grid max-w-6xl grid-cols-2 gap-4 md:grid-cols-5">
            @foreach ($newUploads as $b)
                @include('dashboard._new-card', ['book' => $b])
            @endforeach
        </div>
    </section>
@endsection
