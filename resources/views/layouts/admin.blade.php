<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin — NEVERMORE')</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    @endif
</head>
<body class="min-h-screen bg-[#080d26] font-sans text-white antialiased">
    {{-- ===== Label ADMIN (bar tipis paling atas) ===== --}}
    <div class="bg-[#1b1b1b] text-[#8b8b8b]">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-3 py-1.5 md:px-6">
            <span class="text-[11px] font-semibold uppercase tracking-[0.25em]">Admin</span>
            <nav class="flex items-center gap-3 text-[11px] md:gap-5 md:text-xs">
                <a href="{{ route('dashboard') }}" class="hover:text-white">Lihat Katalog</a>
                <span class="hidden max-w-32 truncate text-slate-400 sm:inline">{{ auth()->user()->fullname ?? '' }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="hover:text-white">Logout</button>
                </form>
            </nav>
        </div>
    </div>

    {{-- ===== Header: logo + pencarian + tombol tambah buku ===== --}}
    <header class="bg-[#141a3f]">
        <div class="mx-auto flex max-w-7xl items-center gap-2 px-3 py-3 md:gap-5 md:px-6">
            <a href="{{ route('admin.dashboard') }}" class="flex h-10 w-10 shrink-0 items-center justify-center bg-[#d7dcf0] md:h-11 md:w-11" title="Admin Home">
                <svg viewBox="0 0 48 48" class="h-8 w-8 md:h-9 md:w-9" fill="none" stroke="#2743a6" stroke-width="3">
                    <path d="M8 10c5-2.5 10-2.5 14 1v26c-4-3.5-9-3.5-14-1V10z" fill="#fff"/>
                    <path d="M40 10c-5-2.5-10-2.5-14 1v26c4-3.5 9-3.5 14-1V10z" fill="#fff"/>
                    <path d="M24 11v26"/>
                    <path d="M5 8h4M5 36l3 4M43 8h-4M43 36l-3 4" stroke-linecap="round"/>
                </svg>
            </a>

            <form action="{{ route('admin.dashboard') }}" method="GET" class="flex min-w-0 flex-1 items-center">
                <input name="q" value="{{ $q ?? '' }}" placeholder="" aria-label="Cari buku"
                    class="h-9 w-full min-w-0 rounded-l bg-[#c7d0e8] px-3 text-sm text-slate-900 focus:outline-none md:h-10">
                <button class="flex h-9 w-10 shrink-0 items-center justify-center rounded-r bg-[#2f4bd7] hover:bg-[#2540bd] md:h-10 md:w-12" title="Cari">
                    <svg viewBox="0 0 24 24" class="h-5 w-5 md:h-6 md:w-6" fill="none" stroke="#fff" stroke-width="3">
                        <circle cx="10" cy="10" r="6"/>
                        <path d="M15 15l6 6" stroke-linecap="round"/>
                    </svg>
                </button>
            </form>

            <button type="button" data-open-add
                class="flex h-9 shrink-0 items-center gap-1.5 rounded bg-[#2f4bd7] px-3 text-sm font-semibold text-white hover:bg-[#2540bd] md:h-10 md:px-5"
                title="Tambah buku baru">
                <span class="text-lg leading-none">+</span>
                <span class="hidden md:inline">Add New Book</span>
            </button>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-3 py-5 md:px-6 md:py-6">
        @yield('content')
    </main>

    @include('admin._book-modal')

    <script>
        (() => {
            const modal = document.getElementById('bookModal');
            const form = document.getElementById('bookForm');
            const title = document.getElementById('bookModalTitle');
            const method = document.getElementById('bookFormMethod');
            const mode = document.getElementById('bookFormMode');
            const bookId = document.getElementById('bookFormId');
            const fileName = document.getElementById('coverFileName');
            const updateUrlTemplate = modal.dataset.updateUrl;

            const fields = ['judul', 'pengarang', 'tags', 'seksi', 'penerbit', 'isbn', 'tahun_terbit', 'jumlah_halaman', 'stok', 'inisial'];

            function setField(name, value) {
                const input = form.elements.namedItem(name);
                if (input) {
                    input.value = value ?? '';
                }
            }

            function openModal(book = null) {
                form.reset();
                fileName.textContent = 'No file chosen';

                if (book) {
                    title.textContent = 'EDIT BOOK';
                    method.value = 'PUT';
                    mode.value = 'edit';
                    bookId.value = book.id;
                    form.action = updateUrlTemplate.replace('__ID__', book.id);
                    fields.forEach((name) => setField(name, book[name]));
                    if (book.cover_image) {
                        fileName.textContent = book.cover_image.split('/').pop();
                    }
                } else {
                    title.textContent = 'ADD NEW BOOK';
                    method.value = 'POST';
                    mode.value = 'create';
                    bookId.value = '';
                    form.action = form.dataset.createUrl;
                }

                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.classList.add('overflow-hidden');
                setTimeout(() => form.elements.namedItem('judul')?.focus(), 60);
            }

            function closeModal() {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.classList.remove('overflow-hidden');
            }

            document.querySelectorAll('[data-open-add]').forEach((button) => {
                button.addEventListener('click', () => openModal());
            });

            document.querySelectorAll('[data-close-modal]').forEach((button) => {
                button.addEventListener('click', closeModal);
            });

            modal.addEventListener('click', (event) => {
                if (event.target === modal) {
                    closeModal();
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                    closeModal();
                }
            });

            document.querySelectorAll('.edit-book').forEach((button) => {
                button.addEventListener('click', () => openModal(JSON.parse(button.dataset.book)));
            });

            form.elements.namedItem('cover_image')?.addEventListener('change', (event) => {
                fileName.textContent = event.target.files[0]?.name ?? 'No file chosen';
            });

            const oldInput = document.getElementById('oldInput');
            if (oldInput) {
                const values = JSON.parse(oldInput.textContent || '{}');

                if (values._form === 'edit' && values._book_id) {
                    openModal({
                        id: values._book_id,
                        judul: values.judul,
                        pengarang: values.pengarang,
                        tags: values.tags,
                        seksi: values.seksi,
                        penerbit: values.penerbit,
                        isbn: values.isbn,
                        tahun_terbit: values.tahun_terbit,
                        jumlah_halaman: values.jumlah_halaman,
                        stok: values.stok,
                        inisial: values.inisial,
                    });
                } else if (values._form === 'create') {
                    openModal();
                    fields.forEach((name) => setField(name, values[name]));
                }
            }
        })();
    </script>
</body>
</html>
