@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white/90">Catat Obat Masuk</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Penerimaan stok obat beserta pembuatan batch baru.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('obat-masuk.index') }}"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-800/80">
                    Kembali
                </a>
            </div>
        </div>

        @if($errors->any())
            <div
                class="rounded-lg bg-error-50 p-4 text-error-800 border border-error-200 dark:bg-error-900/20 dark:text-error-400 dark:border-error-800/30">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-2xl border border-gray-200 bg-white shadow-theme-sm dark:border-gray-800 dark:bg-gray-dark">
            <form action="{{ route('obat-masuk.store') }}" method="POST" class="p-6">
                @csrf
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Obat -->
                    <div>
                        <label for="obat_id"
                            class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Obat</label>
                        <div class="relative">
                            <select id="obat_id" name="obat_id" required
                                class="h-11 w-full appearance-none rounded-lg border border-gray-200 bg-transparent px-4 py-2.5 pr-10 text-sm text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-500 transition duration-150 cursor-pointer">
                                <option value="" disabled selected class="dark:bg-gray-900">Pilih Obat...</option>
                                @foreach($obats as $obat)
                                    <option value="{{ $obat->id }}" {{ old('obat_id') == $obat->id ? 'selected' : '' }}
                                        class="dark:bg-gray-900">
                                        {{ $obat->kode_obat }} - {{ $obat->nama_obat }}
                                    </option>
                                @endforeach
                            </select>
                            <span
                                class="pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    <!-- Supplier -->
                    <div>
                        <label for="supplier_id"
                            class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Supplier</label>
                        <div class="relative">
                            <select id="supplier_id" name="supplier_id" required
                                class="h-11 w-full appearance-none rounded-lg border border-gray-200 bg-transparent px-4 py-2.5 pr-10 text-sm text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-500 transition duration-150 cursor-pointer">
                                <option value="" disabled selected class="dark:bg-gray-900">Pilih Supplier...</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }} class="dark:bg-gray-900">
                                        {{ $supplier->nama_supplier }}
                                    </option>
                                @endforeach
                            </select>
                            <span
                                class="pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    <!-- Nomor Batch -->
                    <div>
                        <div class="mb-2">
                            <label for="nomor_batch" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Nomor Batch
                            </label>
                        </div>
                        <div class="relative">
                            <input type="text" id="nomor_batch" name="nomor_batch" value="{{ old('nomor_batch') }}" readonly
                                class="h-11 w-full rounded-lg border border-gray-200 bg-gray-100 px-4 py-2.5 text-sm font-mono text-gray-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 cursor-not-allowed transition duration-150"
                                placeholder="Otomatis terisi setelah memilih obat...">
                        </div>
                    </div>

                    <!-- Tanggal Kadaluwarsa -->
                    <div>
                        <label for="tanggal_kadaluwarsa"
                            class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Kadaluwarsa
                            (ED)</label>
                        <input type="date" id="tanggal_kadaluwarsa" name="tanggal_kadaluwarsa"
                            value="{{ old('tanggal_kadaluwarsa') }}" required min="{{ date('Y-m-d') }}"
                            class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-500 transition duration-150">
                    </div>

                    <!-- Jumlah -->
                    <div>
                        <label for="jumlah" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah
                            Masuk</label>
                        <input type="number" id="jumlah" name="jumlah" value="{{ old('jumlah') }}" required min="1"
                            class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-gray-500 dark:focus:border-brand-500 transition duration-150"
                            placeholder="Jumlah stok diterima">
                    </div>

                    <!-- Harga Beli -->
                    <div>
                        <label for="harga_beli"
                            class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Harga Beli Total
                            (Rp)</label>
                        <input type="number" id="harga_beli" name="harga_beli" value="{{ old('harga_beli') }}" required
                            min="0" step="100"
                            class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-gray-500 dark:focus:border-brand-500 transition duration-150"
                            placeholder="Contoh: 1500000">
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3">
                    <button type="reset"
                        class="h-11 inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-800/80 transition duration-150">
                        Reset
                    </button>
                    <button type="submit"
                        class="h-11 inline-flex items-center justify-center rounded-lg bg-brand-500 px-6 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-500/30 transition duration-150">
                        Simpan Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const obatSelect = document.getElementById('obat_id');
        const batchInput = document.getElementById('nomor_batch');

        function generateBatchOtomatis() {
            const obatId = obatSelect.value;
            const url = "{{ route('obat-masuk.generate-batch') }}" + (obatId ? '?obat_id=' + obatId : '');

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    if (data && data.nomor_batch) {
                        batchInput.value = data.nomor_batch;
                    }
                })
                .catch(err => console.error('Gagal generate batch:', err));
        }

        obatSelect.addEventListener('change', function () {
            // Otomatis generate nomor batch ketika obat dipilih jika belum diisi manual
            generateBatchOtomatis();
        });

        // Jalankan saat load jika obat sudah terpilih
        if (obatSelect.value && !batchInput.value) {
            generateBatchOtomatis();
        }
    </script>
@endpush