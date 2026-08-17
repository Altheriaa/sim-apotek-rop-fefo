@extends('layouts.app')

@push('styles')
    <style>
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none !important;
            margin: 0 !important;
        }

        input[type=number] {
            -moz-appearance: textfield !important;
            appearance: textfield !important;
        }
    </style>
@endpush

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white/90">Kasir — Point of Sale</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Proses transaksi penjualan obat ke pembeli. Stok akan
                    otomatis terpotong dari rak via FEFO.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('riwayat-penjualan.index') }}"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3.5 py-2 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition">
                    <i class="ti ti-history text-base"></i> Riwayat Penjualan
                </a>
            </div>
        </div>

        {{-- Flash Alerts --}}
        @if(session('success'))
            <div
                class="rounded-xl bg-success-50 p-4 text-success-800 border border-success-200 dark:bg-success-900/20 dark:text-success-400 dark:border-success-800/30 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-3">
                    <i class="ti ti-circle-check text-2xl text-success-500 shrink-0"></i>
                    <div>
                        <p class="text-sm font-bold">{{ session('success') }}</p>
                        <p class="text-xs text-success-600 dark:text-success-400">Struk penjualan otomatis dibuka di tab baru.
                        </p>
                    </div>
                </div>
                @if(session('struk_id'))
                    <a href="{{ route('kasir.struk', session('struk_id')) }}" target="_blank"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-success-600 px-3.5 py-1.5 text-xs font-semibold text-white hover:bg-success-700 transition shadow-sm self-start sm:self-auto">
                        <i class="ti ti-printer text-sm"></i> Buka Struk (Tab Baru)
                    </a>
                @endif
            </div>
        @endif

        @if(session('error'))
            <div
                class="rounded-xl bg-error-50 p-4 text-error-800 border border-error-200 dark:bg-error-900/20 dark:text-error-400 dark:border-error-800/30 flex items-start gap-3">
                <i class="ti ti-alert-circle text-xl text-error-500 shrink-0 mt-0.5"></i>
                <div class="text-sm font-medium">{{ session('error') }}</div>
            </div>
        @endif

        {{-- Dynamic Toast Alert Container --}}
        <div id="toast-container" class="fixed bottom-6 right-6 z-50 flex flex-col gap-2 pointer-events-none"></div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-12 items-start">
            <div class="xl:col-span-7 space-y-4">
                {{-- Search & Filter Bar --}}
                <div
                    class="rounded-xl border border-gray-200 bg-white p-4 shadow-theme-xs dark:border-gray-800 dark:bg-gray-dark space-y-3">
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400">
                            <i class="ti ti-search text-lg"></i>
                        </span>
                        <input type="text" id="search-obat"
                            placeholder="Cari nama atau kode obat... (Tekan '/' untuk fokus)"
                            class="h-11 w-full rounded-lg border border-gray-200 bg-gray-50/50 pl-10 pr-10 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900/60 dark:text-white/90 dark:placeholder:text-gray-500 transition">
                        <button type="button" id="btn-clear-search"
                            class="hidden absolute inset-y-0 right-0 items-center pr-3.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                            <i class="ti ti-x text-base"></i>
                        </button>
                    </div>

                    {{-- Category Pills --}}
                    @if(isset($categories) && $categories->isNotEmpty())
                        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 custom-scrollbar text-xs">
                            <button type="button" onclick="filterKategori('all', this)"
                                class="category-pill active shrink-0 rounded-lg px-3 py-1.5 font-medium transition bg-brand-500 text-white">
                                Semua
                            </button>
                            @foreach($categories as $cat)
                                <button type="button" onclick="filterKategori('{{ strtolower($cat) }}', this)"
                                    class="category-pill shrink-0 rounded-lg border border-gray-200 bg-gray-50 px-3 py-1.5 font-medium text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition">
                                    {{ $cat }}
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Obat Grid --}}
                <div id="obat-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @forelse($obats as $obat)
                        @php
                            $stokRak = (int) $obat->stok_rak_total;
                        @endphp
                        <div class="obat-card group relative flex flex-col justify-between rounded-xl border border-gray-200 bg-white p-4 shadow-theme-xs transition-all duration-200 hover:border-brand-400 hover:shadow-md dark:border-gray-700 dark:bg-gray-dark dark:hover:border-brand-500"
                            id="card-obat-{{ $obat->id }}" data-id="{{ $obat->id }}"
                            data-nama="{{ strtolower($obat->nama_obat) }}" data-kode="{{ strtolower($obat->kode_obat ?? '') }}"
                            data-kategori="{{ strtolower($obat->kategori ?? '') }}" data-stok="{{ $stokRak }}"
                            data-harga="{{ (float) $obat->harga }}" data-satuan="{{ $obat->satuan }}"
                            data-nama-real="{{ $obat->nama_obat }}">

                            {{-- Badge In Cart --}}
                            <div id="cart-badge-{{ $obat->id }}"
                                class="hidden absolute top-2 right-2 flex items-center justify-center h-6 min-w-[24px] px-1.5 rounded-full bg-brand-500 text-white text-xs font-bold shadow">
                                0
                            </div>

                            <div>
                                <div class="flex items-start gap-3">
                                    <div class="min-w-0 flex-1">
                                        @if($obat->kategori)
                                            <span
                                                class="inline-block text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">{{ $obat->kategori }}</span>
                                        @endif
                                        <h3 class="text-sm font-semibold text-gray-800 dark:text-white/90 leading-tight truncate"
                                            title="{{ $obat->nama_obat }}">
                                            {{ $obat->nama_obat }}
                                        </h3>
                                        <span class="text-xs font-mono text-gray-400">{{ $obat->kode_obat ?? '' }}</span>
                                    </div>
                                </div>

                                <div class="mt-3 flex items-center justify-between text-xs">
                                    <span class="text-gray-500 dark:text-gray-400">Stok Rak:</span>
                                    <span
                                        class="font-bold px-2 py-0.5 rounded-md {{ $stokRak > 5 ? 'bg-success-50 text-success-700 dark:bg-success-900/20 dark:text-success-400' : 'bg-warning-50 text-warning-700 dark:bg-warning-900/20 dark:text-warning-400' }}">
                                        {{ $stokRak }} {{ $obat->satuan }}
                                    </span>
                                </div>
                            </div>

                            <div
                                class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between">
                                <div class="text-sm font-bold dark:text-brand-400">
                                    Rp {{ number_format($obat->harga, 0, ',', '.') }}
                                </div>
                                <button type="button" onclick="handleTambahClick({{ $obat->id }})"
                                    class="btn-tambah inline-flex items-center gap-1 rounded-lg bg-brand-50 hover:bg-brand-500 hover:text-white px-2.5 py-1.5 text-xs font-semibold dark:bg-brand-900/30 dark:text-brand-300 dark:hover:bg-brand-500 dark:hover:text-white transition">
                                    <i class="ti ti-plus text-sm"></i> Tambah
                                </button>
                            </div>
                        </div>
                    @empty
                        <div
                            class="col-span-full rounded-xl border border-warning-200 bg-warning-50 p-8 text-center dark:border-warning-800/30 dark:bg-warning-900/10">
                            <i class="ti ti-alert-triangle text-4xl text-warning-500 mb-2 inline-block"></i>
                            <h4 class="text-base font-semibold text-warning-800 dark:text-warning-200">Tidak ada obat tersedia
                                di rak</h4>
                            <p class="text-xs text-warning-700 dark:text-warning-300 mt-1 max-w-md mx-auto">
                                Semua stok rak kosong. Silakan lakukan transfer obat dari gudang ke rak display terlebih dahulu.
                            </p>
                            <a href="{{ route('transfer-rak.create') }}"
                                class="mt-4 inline-flex items-center gap-1.5 rounded-lg bg-warning-500 px-4 py-2 text-xs font-medium text-white hover:bg-warning-600 transition shadow-sm">
                                <i class="ti ti-arrow-right"></i> Transfer dari Gudang ke Rak
                            </a>
                        </div>
                    @endforelse
                </div>

                {{-- Empty Search State --}}
                <div id="search-empty"
                    class="hidden rounded-xl border border-gray-200 bg-white p-8 text-center dark:border-gray-800 dark:bg-gray-dark shadow-theme-xs">
                    <i class="ti ti-search-off text-3xl text-gray-400 mb-2 inline-block"></i>
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Tidak ada obat yang cocok</p>
                    <p class="text-xs text-gray-400 mt-0.5">Coba cari dengan kata kunci lain atau pilih kategori Semua.</p>
                </div>
            </div>

            {{-- Grid Kasir --}}
            <div class="xl:col-span-5">
                <form action="{{ route('kasir.store') }}" method="POST" id="form-kasir">
                    @csrf
                    <div
                        class="rounded-xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-gray-dark sticky top-20 overflow-hidden">

                        {{-- Cart Header --}}
                        <div
                            class="flex items-center justify-between border-b border-gray-100 px-5 py-4 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30">
                            <div class="flex items-center gap-2">
                                <i class="ti ti-shopping-cart text-xl text-brand-500"></i>
                                <h2 class="text-base font-bold text-gray-800 dark:text-white/90">Keranjang Belanja</h2>
                                <span id="cart-count-badge"
                                    class="rounded-full bg-brand-100 text-brand-700 dark:bg-brand-900/40 dark:text-brand-300 px-2 py-0.5 text-xs font-bold">
                                    0 item
                                </span>
                            </div>
                            <button type="button" onclick="clearCart()" id="btn-clear-cart"
                                class="hidden text-xs font-medium text-error-500 hover:text-error-700 hover:underline transition flex items-center gap-1">
                                <i class="ti ti-trash text-sm"></i> Kosongkan
                            </button>
                        </div>

                        {{-- Cart List & Empty Container --}}
                        <div class="p-2 sm:p-4">
                            {{-- Empty State (Preserved in DOM, never destroyed) --}}
                            <div id="cart-empty"
                                class="flex flex-col items-center justify-center py-12 px-4 text-center text-gray-400">
                                <div
                                    class="h-14 w-14 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-3">
                                    <i class="ti ti-shopping-cart-off text-2xl text-gray-400"></i>
                                </div>
                                <p class="text-sm font-semibold text-gray-600 dark:text-gray-300">Keranjang Masih Kosong</p>
                                <p class="text-xs text-gray-400 mt-1 max-w-xs">Pilih obat dari katalog di sebelah kiri untuk
                                    menambahkannya ke transaksi kasir.</p>
                            </div>

                            {{-- Active Items List --}}
                            <div id="cart-items-list"
                                class="hidden max-h-[320px] overflow-y-auto space-y-2 custom-scrollbar pr-1">
                                {{-- Dynamically rendered via JS --}}
                            </div>
                        </div>

                        {{-- Form Inputs & Checkout Summary --}}
                        <div
                            class="border-t border-gray-100 px-5 py-4 dark:border-gray-800 space-y-4 bg-gray-50/30 dark:bg-gray-900/20">

                            {{-- Customer Info --}}
                            <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">Nama
                                        Pembeli</label>
                                    <input type="text" name="nama_pembeli" placeholder="Nama Pasien / Pembeli"
                                        class="h-9 w-full rounded-lg border border-gray-200 bg-white px-3 text-xs text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 transition">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">Catatan /
                                        Resep</label>
                                    <input type="text" name="catatan" placeholder="Catatan transaksi (opsional)"
                                        class="h-9 w-full rounded-lg border border-gray-200 bg-white px-3 text-xs text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 transition">
                                </div>
                            </div>

                            {{-- Total Harga Summary --}}
                            <div
                                class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800 space-y-4.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Total Tagihan</span>
                                    <span id="total-display"
                                        class="text-2xl font-extrabold text-gray-900 dark:text-white">Rp 0</span>
                                </div>

                                {{-- Nominal Bayar Input --}}
                                <div class="pt-3 border-t border-gray-100 dark:border-gray-700 space-y-3">
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">
                                        Nominal Pembayaran (Rp) <span class="text-error-500">*</span>
                                    </label>
                                    <div class="relative flex items-center">
                                        <span
                                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-sm font-bold text-gray-400 dark:text-gray-500 select-none">
                                            Rp
                                        </span>
                                        <input type="number" name="nominal_bayar" id="nominal-bayar" min="0" step="500"
                                            oninput="hitungKembalian()"
                                            class="h-11 w-full rounded-lg border border-gray-200 bg-white pl-12 pr-4 text-base font-bold text-gray-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white transition"
                                            placeholder="0">
                                    </div>

                                    {{-- Quick Cash Buttons --}}
                                    <div class="pt-1 flex flex-wrap items-center gap-2 text-xs">
                                        <button type="button" onclick="setNominalPas()"
                                            class="rounded-md border border-gray-200 bg-gray-50 px-2.5 py-1.5 font-medium text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 transition">
                                            Uang Pas
                                        </button>
                                        <button type="button" onclick="setNominal(10000)"
                                            class="rounded-md border border-gray-200 bg-gray-50 px-2.5 py-1.5 font-medium text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 transition">
                                            10k
                                        </button>
                                        <button type="button" onclick="setNominal(20000)"
                                            class="rounded-md border border-gray-200 bg-gray-50 px-2.5 py-1.5 font-medium text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 transition">
                                            20k
                                        </button>
                                        <button type="button" onclick="setNominal(50000)"
                                            class="rounded-md border border-gray-200 bg-gray-50 px-2.5 py-1.5 font-medium text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 transition">
                                            50k
                                        </button>
                                        <button type="button" onclick="setNominal(100000)"
                                            class="rounded-md border border-gray-200 bg-gray-50 px-2.5 py-1.5 font-medium text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 transition">
                                            100k
                                        </button>
                                    </div>
                                </div>

                                {{-- Kembalian / Status Display --}}
                                <div id="kembalian-box"
                                    class="rounded-lg bg-gray-100 dark:bg-gray-700/50 p-3.5 flex items-center justify-between transition-colors">
                                    <span id="kembalian-label"
                                        class="text-xs font-semibold text-gray-600 dark:text-gray-400">Kembalian</span>
                                    <span id="kembalian-display"
                                        class="text-base font-bold text-gray-800 dark:text-white">Rp 0</span>
                                </div>
                            </div>

                            {{-- Hidden dynamic inputs for backend --}}
                            <input type="hidden" name="total_harga" id="total-hidden" value="0">
                            <div id="cart-hidden-inputs"></div>

                            {{-- Checkout Submit Button --}}
                            <button type="submit" id="btn-checkout"
                                class="w-full h-12 inline-flex items-center justify-center gap-2 rounded-xl bg-brand-500 text-sm font-bold text-white shadow-theme-xs hover:bg-brand-600 transition disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-brand-500 cursor-pointer"
                                disabled>
                                <i class="ti ti-check text-lg"></i> Proses Pembayaran & Cetak Struk
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // State Keranjang Kasir
        let cart = {};
        let totalHarga = 0;
        let currentCategory = 'all';

        // ── Inisialisasi Event Listener ──
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('search-obat');
            const clearSearchBtn = document.getElementById('btn-clear-search');

            // Shortcut '/' untuk cari obat
            document.addEventListener('keydown', function (e) {
                if (e.key === '/' && document.activeElement !== searchInput && !['input', 'textarea'].includes(document.activeElement.tagName.toLowerCase())) {
                    e.preventDefault();
                    searchInput.focus();
                    searchInput.select();
                }
            });

            // Search obat input
            searchInput.addEventListener('input', function () {
                filterObat();
                clearSearchBtn.classList.toggle('hidden', !this.value);
            });

            clearSearchBtn.addEventListener('click', function () {
                searchInput.value = '';
                clearSearchBtn.classList.add('hidden');
                filterObat();
                searchInput.focus();
            });

            // Form submit safety validation
            document.getElementById('form-kasir').addEventListener('submit', function (e) {
                const itemsCount = Object.keys(cart).length;
                if (itemsCount === 0) {
                    e.preventDefault();
                    showToast('Keranjang masih kosong. Tambahkan obat terlebih dahulu.', 'error');
                    return;
                }

                const nominalBayar = parseFloat(document.getElementById('nominal-bayar').value) || 0;
                if (nominalBayar < totalHarga) {
                    e.preventDefault();
                    showToast('Nominal bayar masih kurang dari total harga!', 'error');
                    document.getElementById('nominal-bayar').focus();
                    return;
                }
            });

            // Auto buka struk di tab baru jika ada transaksi sukses
            @if(session('struk_id'))
                try {
                    window.open("{{ route('kasir.struk', session('struk_id')) }}", '_blank');
                } catch (e) {
                    console.log('Popup blocked, user can click banner button');
                }
            @endif

            renderCart();
        });

        // ── Filter Kategori & Search ──
        function filterKategori(kategori, btn) {
            currentCategory = kategori;
            document.querySelectorAll('.category-pill').forEach(el => {
                el.className = 'category-pill shrink-0 rounded-lg border border-gray-200 bg-gray-50 px-3 py-1.5 font-medium text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition';
            });
            btn.className = 'category-pill active shrink-0 rounded-lg px-3 py-1.5 font-medium transition bg-brand-500 text-white';
            filterObat();
        }

        function filterObat() {
            const query = (document.getElementById('search-obat').value || '').toLowerCase().trim();
            const cards = document.querySelectorAll('.obat-card');
            let visibleCount = 0;

            cards.forEach(card => {
                const nama = card.dataset.nama || '';
                const kode = card.dataset.kode || '';
                const kategori = card.dataset.kategori || '';

                const matchQuery = !query || nama.includes(query) || kode.includes(query);
                const matchKategori = currentCategory === 'all' || kategori === currentCategory;

                if (matchQuery && matchKategori) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            const emptySearch = document.getElementById('search-empty');
            if (emptySearch) {
                emptySearch.classList.toggle('hidden', visibleCount > 0 || cards.length === 0);
            }
        }

        // ── Tambah Obat ke Keranjang ──
        function handleTambahClick(id) {
            const card = document.getElementById('card-obat-' + id);
            if (!card) return;

            const nama = card.dataset.namaReal || card.dataset.nama;
            const harga = parseFloat(card.dataset.harga) || 0;
            const stok = parseInt(card.dataset.stok, 10) || 0;
            const satuan = card.dataset.satuan || 'pcs';

            tambahKeCart(id, nama, harga, stok, satuan);
        }

        function tambahKeCart(id, nama, harga, stok, satuan) {
            if (stok <= 0) {
                showToast(`Stok rak ${nama} habis!`, 'error');
                return;
            }

            if (cart[id]) {
                if (cart[id].qty >= stok) {
                    showToast(`Stok rak ${nama} hanya tersedia ${stok} ${satuan}`, 'warning');
                    return;
                }
                cart[id].qty++;
            } else {
                cart[id] = { id, nama, harga, stok, satuan, qty: 1 };
            }

            showToast(`${nama} ditambahkan ke keranjang`, 'success');
            renderCart();
        }

        // ── Modifikasi Quantity ──
        function ubahQty(id, delta) {
            if (!cart[id]) return;

            const newQty = cart[id].qty + delta;
            if (newQty <= 0) {
                hapusItem(id);
                return;
            }

            if (newQty > cart[id].stok) {
                showToast(`Maksimal stok rak ${cart[id].nama} adalah ${cart[id].stok} ${cart[id].satuan}`, 'warning');
                cart[id].qty = cart[id].stok;
            } else {
                cart[id].qty = newQty;
            }

            renderCart();
        }

        function setQtyDirect(id, inputEl) {
            if (!cart[id]) return;

            let val = parseInt(inputEl.value, 10);
            if (isNaN(val) || val <= 0) {
                hapusItem(id);
                return;
            }

            if (val > cart[id].stok) {
                showToast(`Stok rak hanya ${cart[id].stok} ${cart[id].satuan}`, 'warning');
                val = cart[id].stok;
            }

            cart[id].qty = val;
            renderCart();
        }

        // ── Hapus Item dari Keranjang ──
        function hapusItem(id) {
            if (cart[id]) {
                const nama = cart[id].nama;
                delete cart[id];
                showToast(`${nama} dihapus dari keranjang`, 'info');
                renderCart();
            }
        }

        // ── Kosongkan Keranjang ──
        function clearCart() {
            if (Object.keys(cart).length === 0) return;
            cart = {};
            showToast('Keranjang telah dikosongkan', 'info');
            renderCart();
        }

        // ── Render Tampilan Keranjang ──
        function renderCart() {
            const emptyState = document.getElementById('cart-empty');
            const listState = document.getElementById('cart-items-list');
            const hiddenInputs = document.getElementById('cart-hidden-inputs');
            const countBadge = document.getElementById('cart-count-badge');
            const clearBtn = document.getElementById('btn-clear-cart');

            const items = Object.values(cart);
            const totalItemsCount = items.reduce((acc, item) => acc + item.qty, 0);

            countBadge.textContent = `${totalItemsCount} item`;
            clearBtn.classList.toggle('hidden', items.length === 0);

            // Update badge pada katalog card obat
            document.querySelectorAll('[id^="cart-badge-"]').forEach(badge => {
                badge.classList.add('hidden');
                badge.textContent = '0';
            });

            items.forEach(item => {
                const badge = document.getElementById('cart-badge-' + item.id);
                if (badge) {
                    badge.classList.remove('hidden');
                    badge.textContent = item.qty;
                }
            });

            if (items.length === 0) {
                emptyState.classList.remove('hidden');
                listState.classList.add('hidden');
                listState.innerHTML = '';
                hiddenInputs.innerHTML = '';
                totalHarga = 0;
                updateTotals();
                return;
            }

            emptyState.classList.add('hidden');
            listState.classList.remove('hidden');

            let html = '';
            let total = 0;
            let inputsHtml = '';

            items.forEach((item, index) => {
                const subtotal = item.harga * item.qty;
                total += subtotal;

                html += `
                                                                                                <div class="flex flex-col gap-2 rounded-lg border border-gray-100 bg-white p-3 shadow-xs dark:border-gray-700/60 dark:bg-gray-800/80">
                                                                                                    <div class="flex items-start justify-between gap-2">
                                                                                                        <div class="min-w-0 flex-1">
                                                                                                            <h4 class="text-xs font-bold text-gray-800 dark:text-white/90 truncate" title="${item.nama}">
                                                                                                                ${item.nama}
                                                                                                            </h4>
                                                                                                            <div class="text-[11px] text-gray-400">
                                                                                                                Rp ${item.harga.toLocaleString('id-ID')} / ${item.satuan}
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        <button type="button" onclick="hapusItem(${item.id})"
                                                                                                            class="text-gray-400 hover:text-error-500 transition p-1 rounded-md hover:bg-error-50 dark:hover:bg-error-900/20"
                                                                                                            title="Hapus item">
                                                                                                            <i class="ti ti-trash text-sm"></i>
                                                                                                        </button>
                                                                                                    </div>

                                                                                                    <div class="flex items-center justify-between pt-1 border-t border-gray-50 dark:border-gray-700/40">
                                                                                                        <div class="flex items-center gap-1">
                                                                                                            <button type="button" onclick="ubahQty(${item.id}, -1)"
                                                                                                                class="h-7 w-7 rounded-md border border-gray-200 bg-gray-50 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600 flex items-center justify-center text-gray-700 dark:text-gray-200 transition font-bold text-xs">
                                                                                                                <i class="ti ti-minus"></i>
                                                                                                            </button>
                                                                                                            <input type="number" value="${item.qty}" min="1" max="${item.stok}"
                                                                                                                onchange="setQtyDirect(${item.id}, this)"
                                                                                                                class="h-7 w-12 rounded-md border border-gray-200 text-center text-xs font-bold text-gray-800 dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:border-brand-500 focus:outline-none">
                                                                                                            <button type="button" onclick="ubahQty(${item.id}, 1)"
                                                                                                                class="h-7 w-7 rounded-md border border-gray-200 bg-gray-50 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600 flex items-center justify-center text-gray-700 dark:text-gray-200 transition font-bold text-xs ${item.qty >= item.stok ? 'opacity-40 cursor-not-allowed' : ''}">
                                                                                                                <i class="ti ti-plus"></i>
                                                                                                            </button>
                                                                                                        </div>
                                                                                                        <div class="text-right">
                                                                                                            <span class="text-xs font-bold text-brand-600 dark:text-brand-400">
                                                                                                                Rp ${subtotal.toLocaleString('id-ID')}
                                                                                                            </span>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            `;

                inputsHtml += `<input type="hidden" name="items[${index}][obat_id]" value="${item.id}">`;
                inputsHtml += `<input type="hidden" name="items[${index}][jumlah]" value="${item.qty}">`;
            });

            listState.innerHTML = html;
            hiddenInputs.innerHTML = inputsHtml;
            totalHarga = total;
            updateTotals();
        }

        // ── Update Total Tagihan & Validasi Tombol ──
        function updateTotals() {
            document.getElementById('total-display').textContent = 'Rp ' + totalHarga.toLocaleString('id-ID');
            document.getElementById('total-hidden').value = totalHarga;
            hitungKembalian();
        }

        // ── Hitung Kembalian Realtime ──
        function hitungKembalian() {
            const bayarInput = document.getElementById('nominal-bayar');
            const bayar = parseFloat(bayarInput.value) || 0;
            const kembalian = bayar - totalHarga;

            const boxEl = document.getElementById('kembalian-box');
            const labelEl = document.getElementById('kembalian-label');
            const displayEl = document.getElementById('kembalian-display');
            const btnCheckout = document.getElementById('btn-checkout');

            if (totalHarga === 0) {
                boxEl.className = 'rounded-lg bg-gray-100 dark:bg-gray-700/50 p-3 flex items-center justify-between transition-colors';
                labelEl.textContent = 'Kembalian';
                labelEl.className = 'text-xs font-semibold text-gray-600 dark:text-gray-400';
                displayEl.textContent = 'Rp 0';
                displayEl.className = 'text-base font-bold text-gray-800 dark:text-white';
                btnCheckout.disabled = true;
                return;
            }

            if (bayar < totalHarga) {
                const kurang = totalHarga - bayar;
                boxEl.className = 'rounded-lg bg-error-50 dark:bg-error-900/20 border border-error-200 dark:border-error-800/40 p-3 flex items-center justify-between transition-colors';
                labelEl.textContent = 'Kurang';
                labelEl.className = 'text-xs font-semibold text-error-700 dark:text-error-400';
                displayEl.textContent = '- Rp ' + kurang.toLocaleString('id-ID');
                displayEl.className = 'text-base font-bold text-error-600 dark:text-error-400';
                btnCheckout.disabled = true;
            } else if (bayar === totalHarga) {
                boxEl.className = 'rounded-lg bg-success-50 dark:bg-success-900/20 border border-success-200 dark:border-success-800/40 p-3 flex items-center justify-between transition-colors';
                labelEl.textContent = 'Status';
                labelEl.className = 'text-xs font-semibold text-success-700 dark:text-success-400';
                displayEl.textContent = 'Uang Pas';
                displayEl.className = 'text-base font-bold text-success-600 dark:text-success-400';
                btnCheckout.disabled = false;
            } else {
                boxEl.className = 'rounded-lg bg-success-50 dark:bg-success-900/20 border border-success-200 dark:border-success-800/40 p-3 flex items-center justify-between transition-colors';
                labelEl.textContent = 'Kembalian';
                labelEl.className = 'text-xs font-semibold text-success-700 dark:text-success-400';
                displayEl.textContent = 'Rp ' + kembalian.toLocaleString('id-ID');
                displayEl.className = 'text-base font-bold text-success-600 dark:text-success-400';
                btnCheckout.disabled = false;
            }
        }

        // ── Quick Nominals ──
        function setNominalPas() {
            if (totalHarga <= 0) return;
            document.getElementById('nominal-bayar').value = totalHarga;
            hitungKembalian();
        }

        function setNominal(amount) {
            document.getElementById('nominal-bayar').value = amount;
            hitungKembalian();
        }

        // ── Toast Notification Helper ──
        function showToast(message, type = 'info') {
            const container = document.getElementById('toast-container');
            if (!container) return;

            const toast = document.createElement('div');
            const bgColors = {
                success: 'bg-emerald-600 text-white',
                error: 'bg-rose-600 text-white',
                warning: 'bg-amber-500 text-white',
                info: 'bg-gray-800 text-white dark:bg-gray-700'
            };

            const icons = {
                success: 'ti-check',
                error: 'ti-alert-triangle',
                warning: 'ti-alert-circle',
                info: 'ti-info-circle'
            };

            toast.className = `pointer-events-auto flex items-center gap-2 rounded-lg px-4 py-2.5 text-xs font-semibold shadow-lg transition-all duration-300 opacity-0 translate-y-2 ${bgColors[type] || bgColors.info}`;
            toast.innerHTML = `<i class="ti ${icons[type] || icons.info} text-base"></i> <span>${message}</span>`;

            container.appendChild(toast);

            requestAnimationFrame(() => {
                toast.classList.remove('opacity-0', 'translate-y-2');
            });

            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => {
                    if (toast.parentElement) toast.parentElement.removeChild(toast);
                }, 300);
            }, 2500);
        }
    </script>
@endpush