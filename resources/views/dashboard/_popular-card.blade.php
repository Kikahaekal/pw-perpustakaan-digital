{{-- Kartu Popular Now: cover + label abu gelap (sesuai Figma) --}}
<a href="{{ route('books.show', $book['id']) }}" class="block overflow-hidden rounded-lg">
    <div class="bg-gradient-to-br {{ $book['cover'] }} flex aspect-[3/4] items-center justify-center p-2 text-center">
        <div>
            <p class="text-2xl font-black text-white/90">{{ $book['inisial'] }}</p>
            <p class="mt-1 text-[11px] leading-tight font-semibold text-white/85">{{ $book['judul'] }}</p>
        </div>
    </div>
    <div class="bg-[#2e2e2e] py-2.5 text-center text-xs font-semibold">{{ $book['judul'] }}</div>
</a>
