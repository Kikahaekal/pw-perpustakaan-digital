{{-- Kartu New Uploads: cover abu terang + label biru (sesuai Figma) --}}
<a href="{{ route('books.show', $book['id']) }}" class="block overflow-hidden rounded-lg">
    <div class="flex aspect-[3/4] items-center justify-center bg-[#d9d9d9] p-2 text-center">
        <div>
            <p class="text-2xl font-black text-slate-500">{{ $book['inisial'] }}</p>
            <p class="mt-1 line-clamp-2 text-[11px] leading-tight font-semibold text-slate-500">{{ $book['judul'] }}</p>
        </div>
    </div>
    <div class="bg-[#2f4bd7] py-2.5 text-center text-xs font-semibold text-white">{{ $book['judul'] }}</div>
</a>
