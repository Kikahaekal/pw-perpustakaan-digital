{{-- Kartu Popular Now: cover + label abu gelap (sesuai Figma) --}}
<a href="{{ route('books.show', $book['id']) }}" class="block overflow-hidden rounded-lg">
    <div class="relative flex aspect-[3/4] items-center justify-center overflow-hidden bg-gradient-to-br {{ $book['cover'] }} p-2 text-center">
        @if ($book['cover_url'] ?? null)
            <img src="{{ $book['cover_url'] }}" alt="Cover {{ $book['judul'] }}" class="absolute inset-0 h-full w-full object-cover">
        @else
            <div class="relative">
                <p class="text-2xl font-black text-white/90">{{ $book['inisial'] }}</p>
                <p class="mt-1 text-[11px] leading-tight font-semibold text-white/85">{{ $book['judul'] }}</p>
            </div>
        @endif
    </div>
    <div class="bg-[#2e2e2e] py-2.5 text-center text-xs font-semibold">{{ $book['judul'] }}</div>
</a>
