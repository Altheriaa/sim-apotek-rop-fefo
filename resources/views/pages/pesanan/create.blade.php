@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white/90">Buat Pesanan Manual</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Buat Purchase Order (PO) baru. Supplier akan otomatis disesuaikan dengan masing-masing obat yang dipilih.
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('pesanan.index') }}"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-800/80 transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        @if(isset($errors) && $errors->any())
            <div
                class="rounded-lg bg-error-50 p-4 text-error-800 border border-error-200 dark:bg-error-900/20 dark:text-error-400 dark:border-error-800/30">
                <ul class="list-disc pl-5 text-sm space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('pesanan.store') }}" method="POST" id="pesananForm">
            @csrf

            <!-- Informasi Pesanan -->
            <div
                class="rounded-2xl border border-gray-200 bg-white shadow-theme-sm dark:border-gray-800 dark:bg-gray-dark mb-6">
                <div class="border-b border-gray-100 dark:border-gray-800 px-6 py-4">
                    <h2 class="text-base font-semibold text-gray-800 dark:text-white/90">Informasi Pesanan</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Tentukan tanggal pesanan dan catatan tambahan (jika ada).</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- Tanggal Pesan -->
                        <div>
                            <label for="tanggal_pesan"
                                class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Tanggal Pesan <span class="text-error-500">*</span>
                            </label>
                            <input type="date" id="tanggal_pesan" name="tanggal_pesan"
                                value="{{ old('tanggal_pesan', date('Y-m-d')) }}" required
                                class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-500 transition duration-150">
                        </div>

                        <!-- Catatan -->
                        <div>
                            <label for="catatan"
                                class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Catatan Pesanan <span class="text-xs text-gray-400 font-normal">(Opsional)</span>
                            </label>
                            <input type="text" id="catatan" name="catatan"
                                value="{{ old('catatan') }}" placeholder="Contoh: Pesanan mendesak untuk stok akhir pekan"
                                class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-gray-500 dark:focus:border-brand-500 transition duration-150">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Item Obat -->
            <div
                class="rounded-2xl border border-gray-200 bg-white shadow-theme-sm dark:border-gray-800 dark:bg-gray-dark mb-6">
                <div class="border-b border-gray-100 dark:border-gray-800 px-6 py-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-gray-800 dark:text-white/90">Daftar Item Obat</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Pilih obat dan tentukan jumlah pesan. Supplier otomatis terhubung dari master obat.</p>
                    </div>
                    <button type="button" id="btnTambahItem"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-brand-500 px-3.5 py-2 text-sm font-medium text-white hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-500/20 transition">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Item
                    </button>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full min-w-[700px] text-left" id="itemsTable">
                            <thead>
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <th class="px-3 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 w-12">#</th>
                                    <th class="px-3 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 min-w-[260px]">Obat <span class="text-error-500">*</span></th>
                                    <th class="px-3 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 min-w-[200px]">Supplier</th>
                                    <th class="px-3 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 w-28 text-center">Satuan Beli</th>
                                    <th class="px-3 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 w-36">Jumlah Pesan <span class="text-error-500">*</span></th>
                                    <th class="px-3 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 w-14"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody">
                                <!-- Rows will be added by JS -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Empty State -->
                    <div id="emptyState"
                        class="py-10 text-center border-t border-gray-100 dark:border-gray-800 mt-2 hidden">
                        <svg class="mx-auto h-10 w-10 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-400 dark:text-gray-500">Belum ada item obat. Klik tombol <strong>"Tambah Item"</strong> di atas.</p>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Total Item: <span id="totalItemCount" class="font-bold text-gray-800 dark:text-white/90">0</span>
                </div>
                <div class="flex gap-3">
                    <button type="button" id="btnReset"
                        class="h-11 inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-800/80 transition duration-150">
                        Reset
                    </button>
                    <button type="submit"
                        class="h-11 inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-6 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-500/30 transition duration-150">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Pesanan
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        // Data obat dari server
        const obatData = {!! $obatJson !!};

        const itemsBody = document.getElementById('itemsBody');
        const emptyState = document.getElementById('emptyState');
        const totalItemCount = document.getElementById('totalItemCount');
        let rowIndex = 0;

        function buildObatOptions(selectedId) {
            let html = '<option value="" disabled selected class="dark:bg-gray-900">Pilih Obat...</option>';
            obatData.forEach(obat => {
                const sel = selectedId == obat.id ? 'selected' : '';
                html += `<option value="${obat.id}" data-satuan="${obat.satuan_beli}" data-supplier="${obat.supplier_nama}" ${sel} class="dark:bg-gray-900">${obat.kode} - ${obat.nama}</option>`;
            });
            return html;
        }

        function addItemRow(obatId = '', jumlah = '') {
            rowIndex++;
            const tr = document.createElement('tr');
            tr.className = 'border-b border-gray-50 dark:border-gray-800/60 group item-row';
            tr.dataset.index = rowIndex;

            tr.innerHTML = `
                <td class="px-3 py-3 text-sm text-gray-400 dark:text-gray-500 align-middle row-number">${rowIndex}</td>
                <td class="px-3 py-3 align-middle">
                    <div class="relative">
                        <select name="items[${rowIndex}][obat_id]" required
                            class="obat-select h-10 w-full appearance-none rounded-lg border border-gray-200 bg-transparent px-3 pr-8 text-sm text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-500 transition duration-150 cursor-pointer">
                            ${buildObatOptions(obatId)}
                        </select>
                        <span class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </span>
                    </div>
                </td>
                <td class="px-3 py-3 align-middle">
                    <div class="supplier-container">
                        <span class="supplier-label inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded-md bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                            — Belum dipilih —
                        </span>
                    </div>
                </td>
                <td class="px-3 py-3 align-middle text-center">
                    <span class="satuan-label inline-flex items-center justify-center h-10 text-sm font-medium text-gray-600 dark:text-gray-300">—</span>
                </td>
                <td class="px-3 py-3 align-middle">
                    <input type="number" name="items[${rowIndex}][jumlah_pesan]" value="${jumlah}" required min="1" placeholder="0"
                        class="h-10 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-gray-500 dark:focus:border-brand-500 transition duration-150">
                </td>
                <td class="px-3 py-3 align-middle text-center">
                    <button type="button" class="btn-hapus p-2 rounded-lg text-gray-400 hover:text-error-500 hover:bg-error-50 dark:hover:bg-error-900/20 dark:hover:text-error-400 transition" title="Hapus item">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </td>
            `;

            itemsBody.appendChild(tr);

            // Event: update satuan and supplier when obat changes
            const obatSelect = tr.querySelector('.obat-select');
            const satuanLabel = tr.querySelector('.satuan-label');
            const supplierContainer = tr.querySelector('.supplier-container');

            obatSelect.addEventListener('change', function () {
                const opt = this.options[this.selectedIndex];
                satuanLabel.textContent = opt.dataset.satuan || '—';
                const suppName = opt.dataset.supplier;
                if (suppName) {
                    supplierContainer.innerHTML = `
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-md bg-brand-50 text-brand-700 dark:bg-brand-900/30 dark:text-brand-400">
                            <svg class="w-3 h-3 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            ${suppName}
                        </span>
                    `;
                } else {
                    supplierContainer.innerHTML = `
                        <span class="inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded-md bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                            — Belum ditentukan —
                        </span>
                    `;
                }
            });

            // Trigger if pre-selected
            if (obatId) {
                obatSelect.dispatchEvent(new Event('change'));
            }

            // Event: hapus row
            tr.querySelector('.btn-hapus').addEventListener('click', function () {
                tr.remove();
                reindexRows();
                toggleEmptyState();
            });

            toggleEmptyState();
        }

        function reindexRows() {
            const rows = itemsBody.querySelectorAll('.item-row');
            rows.forEach((row, i) => {
                row.querySelector('.row-number').textContent = i + 1;
            });
            totalItemCount.textContent = rows.length;
        }

        function toggleEmptyState() {
            const rows = itemsBody.querySelectorAll('.item-row');
            const count = rows.length;
            emptyState.classList.toggle('hidden', count > 0);
            totalItemCount.textContent = count;
        }

        // Button: Tambah Item
        document.getElementById('btnTambahItem').addEventListener('click', () => addItemRow());

        // Button: Reset
        document.getElementById('btnReset').addEventListener('click', () => {
            document.getElementById('tanggal_pesan').value = '{{ date('Y-m-d') }}';
            const catatan = document.getElementById('catatan');
            if (catatan) catatan.value = '';
            itemsBody.innerHTML = '';
            rowIndex = 0;
            addItemRow();
            toggleEmptyState();
        });

        // Form validation
        document.getElementById('pesananForm').addEventListener('submit', function (e) {
            const rows = itemsBody.querySelectorAll('.item-row');
            if (rows.length === 0) {
                e.preventDefault();
                alert('Tambahkan minimal 1 item obat sebelum menyimpan pesanan.');
                return;
            }
        });

        // Init: add 1 empty row on load
        addItemRow();
    </script>
@endpush