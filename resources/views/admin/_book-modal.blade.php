{{-- Modal tambah/edit buku (desain: panel ungu transparan) --}}
<div id="bookModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-3 backdrop-blur-sm"
    data-update-url="{{ route('admin.books.update', ['buku' => '__ID__']) }}">
    <div class="max-h-[92vh] w-full max-w-md overflow-y-auto rounded-2xl bg-[#4b418f]/95 p-5 shadow-2xl ring-1 ring-white/20 md:p-6">
        <div class="flex items-start justify-between gap-4">
            <h2 id="bookModalTitle" class="text-xl font-black tracking-wide">ADD NEW BOOK</h2>
            <button type="button" data-close-modal class="text-2xl leading-none text-white/70 hover:text-white" aria-label="Tutup">&times;</button>
        </div>

        <form id="bookForm" method="POST" action="{{ route('admin.books.store') }}" enctype="multipart/form-data"
            data-create-url="{{ route('admin.books.store') }}" class="mt-3 space-y-3">
            @csrf
            <input type="hidden" name="_method" id="bookFormMethod" value="POST">
            <input type="hidden" name="_form" id="bookFormMode" value="create">
            <input type="hidden" name="_book_id" id="bookFormId" value="">

            <div>
                <label for="judul" class="mb-1 block text-sm font-semibold">Book Title</label>
                <input id="judul" name="judul" type="text" required maxlength="255" autocomplete="off"
                    class="h-11 w-full rounded-lg border-2 border-white/90 bg-[#1b1f4d] px-3 text-sm text-white placeholder-white/40 focus:border-white focus:outline-none">
            </div>

            <div>
                <label for="pengarang" class="mb-1 block text-sm font-semibold">Author</label>
                <input id="pengarang" name="pengarang" type="text" required maxlength="255" autocomplete="off" placeholder="Author"
                    class="h-11 w-full rounded-lg border-2 border-white/90 bg-[#1b1f4d] px-3 text-sm text-white placeholder-white/40 focus:border-white focus:outline-none">
            </div>

            <div>
                <label for="tags" class="mb-1 block text-sm font-semibold">Tags</label>
                <input id="tags" name="tags" type="text" maxlength="1000" autocomplete="off" placeholder="Horror, Misteri"
                    class="h-11 w-full rounded-lg border-2 border-white/90 bg-[#1b1f4d] px-3 text-sm text-white placeholder-white/40 focus:border-white focus:outline-none">
                <p class="mt-1 text-[10px] text-white/60">Pisahkan beberapa tag dengan koma.</p>
            </div>

            <div>
                <label for="seksi" class="mb-1 block text-sm font-semibold">Section</label>
                <select id="seksi" name="seksi"
                    class="h-11 w-full rounded-lg border-2 border-white/90 bg-[#1b1f4d] px-3 text-sm text-white focus:border-white focus:outline-none">
                    <option value="">-Select Section-</option>
                    @foreach (\App\Models\Buku::SEKSI_LABELS as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <details class="rounded-lg bg-black/10 px-3 py-2 ring-1 ring-white/10">
                <summary class="cursor-pointer text-xs font-semibold text-white/80">Detail tambahan (penerbit, tahun, stok)</summary>
                <div class="mt-3 grid grid-cols-2 gap-3">
                    <div class="col-span-2">
                        <label for="penerbit" class="mb-1 block text-xs font-semibold">Penerbit</label>
                        <input id="penerbit" name="penerbit" type="text" maxlength="255"
                            class="h-9 w-full rounded-lg border border-white/60 bg-[#1b1f4d] px-3 text-xs text-white placeholder-white/40 focus:border-white focus:outline-none">
                    </div>
                    <div>
                        <label for="tahun_terbit" class="mb-1 block text-xs font-semibold">Tahun Terbit</label>
                        <input id="tahun_terbit" name="tahun_terbit" type="number" min="0" max="{{ now()->year }}"
                            class="h-9 w-full rounded-lg border border-white/60 bg-[#1b1f4d] px-3 text-xs text-white focus:border-white focus:outline-none">
                    </div>
                    <div>
                        <label for="stok" class="mb-1 block text-xs font-semibold">Stok</label>
                        <input id="stok" name="stok" type="number" min="0"
                            class="h-9 w-full rounded-lg border border-white/60 bg-[#1b1f4d] px-3 text-xs text-white focus:border-white focus:outline-none">
                    </div>
                    <div>
                        <label for="jumlah_halaman" class="mb-1 block text-xs font-semibold">Halaman</label>
                        <input id="jumlah_halaman" name="jumlah_halaman" type="number" min="1"
                            class="h-9 w-full rounded-lg border border-white/60 bg-[#1b1f4d] px-3 text-xs text-white focus:border-white focus:outline-none">
                    </div>
                    <div>
                        <label for="isbn" class="mb-1 block text-xs font-semibold">ISBN</label>
                        <input id="isbn" name="isbn" type="text" maxlength="255"
                            class="h-9 w-full rounded-lg border border-white/60 bg-[#1b1f4d] px-3 text-xs text-white placeholder-white/40 focus:border-white focus:outline-none">
                    </div>
                    <div class="col-span-2">
                        <label for="inisial" class="mb-1 block text-xs font-semibold">Inisial Cover</label>
                        <input id="inisial" name="inisial" type="text" maxlength="8"
                            class="h-9 w-full rounded-lg border border-white/60 bg-[#1b1f4d] px-3 text-xs text-white placeholder-white/40 focus:border-white focus:outline-none">
                    </div>
                </div>
            </details>

            <div>
                <p class="mb-1 text-sm font-semibold">Cover Image</p>
                <div class="flex flex-wrap items-center gap-3">
                    <label class="cursor-pointer rounded bg-white px-3 py-1.5 text-xs font-semibold text-slate-800 hover:bg-slate-100">
                        Upload File
                        <input id="cover_image" name="cover_image" type="file" accept="image/*" class="sr-only">
                    </label>
                    <span id="coverFileName" class="min-w-0 flex-1 truncate text-[10px] text-white/70">No file chosen</span>
                    <button type="submit" class="rounded bg-[#22c55e] px-6 py-1.5 text-sm font-bold text-white hover:bg-[#16a34a]">SAVE</button>
                </div>
            </div>
        </form>

        <form id="deleteBookForm" method="POST" action="#" class="mt-2 hidden"
            data-delete-url="{{ route('admin.books.destroy', ['buku' => '__ID__']) }}"
            onsubmit="return confirm('Hapus buku ini? Tindakan ini tidak dapat dibatalkan.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="w-full rounded border border-red-300/50 bg-red-500/20 px-4 py-2 text-xs font-bold uppercase tracking-wide text-red-100 hover:bg-red-500/35">
                Hapus Buku
            </button>
        </form>
    </div>
</div>
