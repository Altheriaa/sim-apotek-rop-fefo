@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white/90">Buat Surat Pesanan (PO)</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Pilih supplier tujuan dan susun daftar obat yang akan dipesan.</p>
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
            <div class="rounded-2xl border border-gray-200 bg-white shadow-theme-sm dark:border-gray-800 dark:bg-gray-dark mb-6">
                <div class="border-b border-gray-100 dark:border-gray-800 px-6 py-4">
                    <h2 class="text-base font-semibold text-gray-800 dark:text-white/90">Informasi Pemesanan</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Tentukan supplier distributor tujuan dan tanggal pembuatan surat pesanan.</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                        <!-- Supplier Tujuan -->
                        <div>
                            <label for="supplier_id" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Supplier Distributor <span class="text-xs text-gray-400 font-normal">(Opsional / Bisa dipilih nanti)</span>
                            </label>
                            <select id="supplier_id" name="supplier_id"
                                class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-500 transition duration-150 cursor-pointer">
                                <option value="" {{ old('supplier_id') ? '' : 'selected' }} class="dark:bg-gray-900">Belum Ditentukan (Pilih Nanti)</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }} class="dark:bg-gray-900">
                                        {{ $supplier->nama_supplier }} {{ $supplier->kontak ? '(' . $supplier->kontak . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tanggal Pesan -->
                        <div>
                            <label for="tanggal_pesan" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Tanggal Pesan <span class="text-error-500">*</span>
                            </label>
                            <input type="date" id="tanggal_pesan" name="tanggal_pesan"
                                value="{{ old('tanggal_pesan', date('Y-m-d')) }}" required
                                class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-500 transition duration-150">
                        </div>

                        <!-- Catatan -->
                        <div>
                            <label for="catatan" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Catatan Pesanan <span class="text-xs text-gray-400 font-normal">(Opsional)</span>
                            </label>
                            <input type="text" id="catatan" name="catatan"
                                value="{{ old('catatan') }}" placeholder="Contoh: Pesanan reguler mingguan"
                                class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-gray-500 dark:focus:border-brand-500 transition duration-150">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Item Obat -->
            <div class="rounded-2xl border border-gray-200 bg-white shadow-theme-sm dark:border-gray-800 dark:bg-gray-dark mb-6">
                <div class="border-b border-gray-100 dark:border-gray-800 px-6 py-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-gray-800 dark:text-white/90">Daftar Item Obat</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Tambahkan obat-obatan yang ingin dipesan ke supplier di atas.</p>
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
                        <table class="w-full min-w-[760px] text-left" id="itemsTable">
                            <thead>
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <th class="px-3 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 w-12 text-center">#</th>
                                    <th class="px-3 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 min-w-[280px]">Obat <span class="text-error-500">*</span></th>
                                    <th class="px-3 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 w-28 text-center">Satuan Beli</th>
                                    <th class="px-3 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 w-36 text-right">Est. Harga Satuan</th>
                                    <th class="px-3 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 w-32">Jumlah Pesan <span class="text-error-500">*</span></th>
                                    <th class="px-3 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 w-36 text-right">Subtotal Est.</th>
                                    <th class="px-3 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 w-14 text-center"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody">
                                <!-- Rows generated by JS -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Empty State -->
                    <div id="emptyState" class="py-10 text-center border-t border-gray-100 dark:border-gray-800 mt-2 hidden">
                        <svg class="mx-auto h-10 w-10 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-400 dark:text-gray-500">Belum ada item obat. Klik tombol <strong>"Tambah Item"</strong> di atas.</p>
                    </div>
                </div>
            </div>

            <!-- Footer Summary & Submit -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-sm dark:border-gray-800 dark:bg-gray-dark">
                <div class="flex items-center gap-6 text-sm">
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Total Item:</span>
                        <span id="totalItemCount" class="ml-1.5 font-bold text-gray-800 dark:text-white/90">0</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Total Estimasi:</span>
                        <span id="grandTotalLabel" class="ml-1.5 font-bold text-brand-600 dark:text-brand-400 text-base">Rp 0</span>
                    </div>
                </div>
                <div class="flex gap-3 w-full sm:w-auto justify-end">
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
        const obatData = {!! $obatJson !!};
        const itemsBody = document.getElementById('itemsBody');
        const emptyState = document.getElementById('emptyState');
        const totalItemCount = document.getElementById('totalItemCount');
        const grandTotalLabel = document.getElementById('grandTotalLabel');
        let rowIndex = 0;

        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(number);
        }

        function buildObatOptions(selectedId) {
            let html = '<option value="" disabled selected class="dark:bg-gray-900">Pilih Obat...</option>';
            obatData.forEach(obat => {
                const sel = selectedId == obat.id ? 'selected' : '';
                html += `<option value="${obat.id}" data-satuan="${obat.satuan_beli}" data-harga="${obat.estimasi_harga}" ${sel} class="dark:bg-gray-900">${obat.kode} - ${obat.nama}</option>`;
            });
            return html;
        }

        function addItemRow(obatId = '', jumlah = '') {
            rowIndex++;
            const tr = document.createElement('tr');
            tr.className = 'border-b border-gray-50 dark:border-gray-800/60 group item-row';
            tr.dataset.index = rowIndex;

            tr.innerHTML = `
                <td class="px-3 py-3 text-sm text-center text-gray-400 dark:text-gray-500 align-middle row-number">${rowIndex}</td>
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
                <td class="px-3 py-3 align-middle text-center">
                    <span class="satuan-label inline-flex items-center justify-center h-10 text-sm font-medium text-gray-600 dark:text-gray-300">-</span>
                </td>
                <td class="px-3 py-3 align-middle text-right">
                    <span class="harga-label text-sm text-gray-700 dark:text-gray-300">Rp 0</span>
                </td>
                <td class="px-3 py-3 align-middle">
                    <input type="number" name="items[${rowIndex}][jumlah_pesan]" value="${jumlah}" required min="1" placeholder="0"
                        class="input-jumlah h-10 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-gray-500 dark:focus:border-brand-500 transition duration-150 text-right">
                </td>
                <td class="px-3 py-3 align-middle text-right font-medium text-gray-800 dark:text-white/90 subtotal-label">
                    Rp 0
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

            const obatSelect = tr.querySelector('.obat-select');
            const satuanLabel = tr.querySelector('.satuan-label');
            const hargaLabel = tr.querySelector('.harga-label');
            const inputJumlah = tr.querySelector('.input-jumlah');
            const subtotalLabel = tr.querySelector('.subtotal-label');

            function updateRowCalculations() {
                const opt = obatSelect.options[obatSelect.selectedIndex];
                const harga = opt ? parseFloat(opt.dataset.harga || 0) : 0;
                const jumlah = parseInt(inputJumlah.value || 0, 10);
                const subtotal = harga * jumlah;

                satuanLabel.textContent = (opt && opt.dataset.satuan) ? opt.dataset.satuan : '-';
                hargaLabel.textContent = formatRupiah(harga);
                subtotalLabel.textContent = formatRupiah(subtotal);
                subtotalLabel.dataset.subtotal = subtotal;

                updateGrandTotal();
            }

            obatSelect.addEventListener('change', updateRowCalculations);
            inputJumlah.addEventListener('input', updateRowCalculations);

            if (obatId) {
                obatSelect.dispatchEvent(new Event('change'));
            }

            tr.querySelector('.btn-hapus').addEventListener('click', function () {
                tr.remove();
                reindexRows();
                toggleEmptyState();
                updateGrandTotal();
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

        function updateGrandTotal() {
            const rows = itemsBody.querySelectorAll('.item-row');
            let grandTotal = 0;
            rows.forEach(row => {
                const subtotal = parseFloat(row.querySelector('.subtotal-label').dataset.subtotal || 0);
                grandTotal += subtotal;
            });
            grandTotalLabel.textContent = formatRupiah(grandTotal);
        }

        document.getElementById('btnTambahItem').addEventListener('click', () => addItemRow());

        document.getElementById('btnReset').addEventListener('click', () => {
            document.getElementById('tanggal_pesan').value = '{{ date('Y-m-d') }}';
            document.getElementById('supplier_id').selectedIndex = 0;
            const catatan = document.getElementById('catatan');
            if (catatan) catatan.value = '';
            itemsBody.innerHTML = '';
            rowIndex = 0;
            addItemRow();
            toggleEmptyState();
            updateGrandTotal();
        });

        document.getElementById('pesananForm').addEventListener('submit', function (e) {
            const rows = itemsBody.querySelectorAll('.item-row');
            if (rows.length === 0) {
                e.preventDefault();
                alert('Tambahkan minimal 1 item obat sebelum menyimpan pesanan.');
                return;
            }
        });

        addItemRow();
    </script>
@endpush