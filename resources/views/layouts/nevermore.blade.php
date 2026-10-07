<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'NEVERMORE — Perpustakaan Digital')</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    @endif
</head>
<body class="min-h-screen bg-[#0d1330] font-sans text-white antialiased">
    {{-- ===== Header: logo + search + hamburger (sesuai Figma) ===== --}}
    <header class="bg-[#161c44]">
        <div class="flex items-center gap-3 px-3 py-3 md:gap-6 md:px-8">
            <a href="{{ route('dashboard') }}" class="flex h-11 w-11 shrink-0 items-center justify-center bg-[#d7dcf0]" title="NEVERMORE Home">
                <svg viewBox="0 0 48 48" class="h-9 w-9" fill="none" stroke="#2743a6" stroke-width="3">
                    <path d="M8 10c5-2.5 10-2.5 14 1v26c-4-3.5-9-3.5-14-1V10z" fill="#fff"/>
                    <path d="M40 10c-5-2.5-10-2.5-14 1v26c4-3.5 9-3.5 14-1V10z" fill="#fff"/>
                    <path d="M24 11v26"/>
                    <path d="M5 8h4M5 36l3 4M43 8h-4M43 36l-3 4" stroke-linecap="round"/>
                </svg>
            </a>
            <form action="{{ route('dashboard') }}" method="GET" class="flex w-full max-w-xl">
                <input name="q" value="{{ $q ?? '' }}" placeholder=""
                    class="h-10 w-full rounded-l bg-[#c7d0e8] px-3 text-sm text-slate-900 focus:outline-none">
                <button class="flex h-10 w-12 items-center justify-center rounded-r bg-[#2f4bd7] hover:bg-[#2540bd]" title="Cari">
                    <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="#fff" stroke-width="3">
                        <circle cx="10" cy="10" r="6"/>
                        <path d="M15 15l6 6" stroke-linecap="round"/>
                    </svg>
                </button>
            </form>
            <button id="menuBtn" class="ml-auto flex h-10 w-10 shrink-0 items-center justify-center rounded bg-[#2f4bd7]" title="Menu">
                <svg viewBox="0 0 24 24" class="h-6 w-6" stroke="#fff" stroke-width="2.5">
                    <path id="menuIconOpen" d="M4 7h16M4 12h16M4 17h16" stroke-linecap="round"/>
                    <path id="menuIconClose" d="M7 7h2M11 7h2M15 7h2M7 12h2M11 12h2M15 12h2M7 17h2M11 17h2M15 17h2" stroke-linecap="round" class="hidden"/>
                </svg>
            </button>
        </div>
        {{-- Dropdown menu mobile (Home tertutup → hamburger): Random, Tags, Author, Info, Favorites --}}
        <nav id="mobileMenu" class="hidden bg-[#232a52] px-6 py-4 text-center text-sm font-semibold">
            <a href="{{ route('dashboard') }}" class="block py-2 hover:text-indigo-300">Home</a>
            <a href="{{ route('dashboard') }}#random" class="block py-2 hover:text-indigo-300">Random</a>
            <a href="{{ route('dashboard') }}#tags" class="block py-2 hover:text-indigo-300">Tags</a>
            <a href="{{ route('dashboard') }}#author" class="block py-2 hover:text-indigo-300">Author</a>
            <a href="{{ route('dashboard') }}#info" class="block py-2 hover:text-indigo-300">Info</a>
            <a href="{{ route('dashboard', ['favorites' => 1]) }}" class="block py-2 hover:text-indigo-300">❤️ Favorites</a>
        </nav>
    </header>

    <div class="flex">
        {{-- ===== Sidebar (desktop: muncul saat hamburger diklik, sesuai Figma) ===== --}}
        <aside id="sidebar" class="hidden w-44 shrink-0 bg-[#232a52] text-center text-sm font-semibold">
            <nav class="py-6">
                <a href="{{ route('dashboard') }}" class="block py-2.5 hover:bg-white/10">Home</a>
                <a href="{{ route('dashboard') }}#random" class="block py-2.5 hover:bg-white/10">Random</a>
                <a href="{{ route('dashboard') }}#tags" class="block py-2.5 hover:bg-white/10">Tags</a>
                <a href="{{ route('dashboard') }}#author" class="block py-2.5 hover:bg-white/10">Author</a>
                <a href="{{ route('dashboard') }}#info" class="block py-2.5 hover:bg-white/10">Info</a>
                <a href="{{ route('dashboard', ['favorites' => 1]) }}" class="block py-2.5 hover:bg-white/10">❤️ Favorites</a>
            </nav>
            <div class="border-t border-white/10 px-3 py-4 text-[11px] font-normal text-slate-300">
                <p class="truncate">{{ auth()->user()->fullname ?? '' }}</p>
                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button class="w-full rounded bg-white/10 py-1.5 text-xs font-semibold hover:bg-white/20">Logout</button>
                </form>
            </div>
        </aside>

        {{-- ===== Konten ===== --}}
        <main class="min-h-screen flex-1 bg-black/60">
            @yield('content')
        </main>
    </div>

    <script>
        const btn = document.getElementById('menuBtn');
        const sidebar = document.getElementById('sidebar');
        const mobileMenu = document.getElementById('mobileMenu');
        const iconOpen = document.getElementById('menuIconOpen');
        const iconClose = document.getElementById('menuIconClose');
        btn.addEventListener('click', () => {
            const isDesktop = window.innerWidth >= 768;
            if (isDesktop) {
                sidebar.classList.toggle('hidden');
            } else {
                mobileMenu.classList.toggle('hidden');
            }
            iconOpen.classList.toggle('hidden');
            iconClose.classList.toggle('hidden');
        });
    </script>
</body>
</html>
