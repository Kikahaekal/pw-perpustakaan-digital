{{-- Judul section ala Figma: ikon buku + NEVERMORE kecil + teks besar --}}
<div class="flex flex-col items-center">
    <svg viewBox="0 0 96 40" class="h-6 w-14" fill="none" stroke="#fff" stroke-width="2.5">
        <text x="48" y="10" text-anchor="middle" font-size="8" fill="#fff" stroke="none" font-weight="bold">NEVERMORE</text>
        <path d="M30 16c6-3 12-3 18 2v14c-6-5-12-5-18-2V16z" fill="none"/>
        <path d="M66 16c-6-3-12-3-18 2v14c6-5 12-5 18-2V16z" fill="none"/>
    </svg>
    <h2 class="mt-1 text-xl font-bold tracking-wide md:text-2xl">{{ $slot }}</h2>
</div>
