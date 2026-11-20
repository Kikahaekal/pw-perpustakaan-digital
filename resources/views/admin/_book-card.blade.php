@php
    $editData = [
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
    ];
@endphp

{{-- Kartu buku admin: cover + label biru + tombol edit --}}
<div class="group relative">
    <a href="{{ route('books.show', $buku->id) }}" class="block overflow-hidden rounded-lg ring-1 ring-white/5 transition hover:ring-[#5b7bff]">
        <div class="relative flex aspect-[3/4] items-center justify-center overflow-hidden bg-[#d9d9d9] p-2 text-center">
            @if ($buku->cover_url)
                <img src="{{ $buku->cover_url }}" alt="Cover {{ $buku->judul }}" class="absolute inset-0 h-full w-full object-cover">
            @else
                <div class="absolute inset-0 bg-gradient-to-br {{ $buku->cover ?? 'from-slate-700 via-slate-900 to-black' }}"></div>
                <div class="relative">
                    <p class="text-2xl font-black text-white/90">{{ $buku->inisial }}</p>
                    <p class="mt-1 line-clamp-2 text-[11px] font-semibold leading-tight text-white/85">{{ $buku->judul }}</p>
                </div>
            @endif

            @if ($buku->seksiLabel())
                <span class="absolute left-1.5 top-1.5 rounded bg-black/60 px-1.5 py-0.5 text-[8px] font-semibold uppercase tracking-wide text-white md:text-[9px]">
                    {{ $buku->seksiLabel() }}
                </span>
            @endif
        </div>
        <div class="bg-[#2f4bd7] px-2 py-2 text-center text-[11px] font-semibold leading-tight text-white">
            {{ $buku->judul }}
        </div>
    </a>

    <button type="button"
        class="edit-book absolute right-1.5 top-1.5 rounded bg-[#2f4bd7] px-2 py-1 text-[9px] font-bold text-white shadow ring-1 ring-white/30 transition hover:bg-[#2540bd] md:text-[10px] md:opacity-0 md:group-hover:opacity-100"
        data-book="{{ e(json_encode($editData)) }}" title="Edit {{ $buku->judul }}">
        Edit
    </button>
</div>
