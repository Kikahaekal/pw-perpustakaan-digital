@props(['label', 'value', 'caption' => null])

{{-- Kartu statistik bergradasi ungu (sesuai desain admin) --}}
<div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-[#241a52] via-[#2b1a60] to-[#3b1d70] px-3 py-3.5 shadow-lg ring-1 ring-white/5 md:px-4 md:py-4">
    <div class="pointer-events-none absolute -right-8 -top-10 h-24 w-24 rounded-full bg-white/10 blur-2xl"></div>
    <p class="relative text-[10px] font-semibold leading-tight text-slate-200/90 md:text-[11px]">{{ $label }}</p>
    <p class="relative mt-1 text-2xl font-extrabold tracking-tight md:text-3xl">{{ $value }}</p>
    @if ($caption)
        <p class="relative mt-0.5 text-[9px] text-slate-300/60 md:text-[10px]">{{ $caption }}</p>
    @endif
</div>
