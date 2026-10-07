<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'NEVERMORE')</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    @endif
</head>
<body class="min-h-screen bg-white font-sans text-slate-800 antialiased">
    <main class="mx-auto flex min-h-screen w-full max-w-lg flex-col items-center px-6 py-10">
        {{-- Logo NEVERMORE melengkung + ikon buku (sesuai Figma) --}}
        <svg viewBox="0 0 300 70" class="w-64">
            <defs>
                <path id="arc" d="M 30 60 Q 150 -10 270 60" fill="none"/>
            </defs>
            <text font-size="30" font-weight="800" fill="#2743a6" letter-spacing="2">
                <textPath href="#arc" startOffset="50%" text-anchor="middle">NEVERMORE</textPath>
            </text>
        </svg>
        <svg viewBox="0 0 96 72" class="mt-1 h-28 w-36" fill="none" stroke="#2743a6" stroke-width="5">
            <path d="M14 12c10-5 20-5 28 3v44c-8-8-18-8-28-3V12z" fill="#fff"/>
            <path d="M82 12c-10-5-20-5-28 3v44c8-8 18-8 28-3V12z" fill="#fff"/>
            <path d="M48 15v44"/>
            <path d="M8 10h8M8 58l6 8M88 10h-8M88 58l-6 8" stroke-linecap="round"/>
        </svg>
        <p class="mt-5 text-center text-2xl font-bold text-[#2743a6]">Abandon all hope, ye who enter here</p>

        @yield('content')
    </main>
</body>
</html>
