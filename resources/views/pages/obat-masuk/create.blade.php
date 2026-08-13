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
            <a href="{{ route('obat-masuk.index') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-800/80">
                Kembali
            </a>
        </div>
    </div>

    @if($errors->any())
    <div class="rounded-lg bg-error-50 p-4 text-error-800 border border-error-200 dark:bg-error-900/20 dark:text-error-400 dark:border-error-800/30">
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
                    <label for="obat_id" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Obat</label>
                    <select id="obat_id" name="obat_id" required
                            class="w-full rounded-lg border border-gray-200 bg-transparent px-4 py-3 text-sm text-gray-800 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-800 dark:text-white/90 dark:focus:border-brand-800 dark:bg-gray-900">
                        <option value="" disabled selected>Pilih Obat...</option>
                        @foreach($obats as $obat)
                            <option value="{{ $obat->id }}" {{ old('obat_id') == $obat->id ? 'selected' : '' }}>
                                {{ $obat->kode_obat }} - {{ $obat->nama_obat }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Supplier -->
                <div>
                    <label for="supplier_id" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Supplier</label>
                    <select id="supplier_id" name="supplier_id" required
                            class="w-full rounded-lg border border-gray-200 bg-transparent px-4 py-3 text-sm text-gray-800 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-800 dark:text-white/90 dark:focus:border-brand-800 dark:bg-gray-900">
                        <option value="" disabled selected>Pilih Supplier...</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->nama_supplier }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Nomor Batch -->
                <div>
                    <label for="nomor_batch" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor Batch</label>
                    <input type="text" id="nomor_batch" name="nomor_batch" value="{{ old('nomor_batch') }}" required
                           class="w-full rounded-lg border border-gray-200 bg-transparent px-4 py-3 text-sm text-gray-800 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-800 dark:text-white/90 dark:focus:border-brand-800"
                           placeholder="Contoh: BATCH-001">
                </div>

                <!-- Tanggal Kadaluwarsa -->
                <div>
                    <label for="tanggal_kadaluwarsa" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Kadaluwarsa (ED)</label>
                    <input type="date" id="tanggal_kadaluwarsa" name="tanggal_kadaluwarsa" value="{{ old('tanggal_kadaluwarsa') }}" required min="{{ date('Y-m-d') }}"
                           class="w-full rounded-lg border border-gray-200 bg-transparent px-4 py-3 text-sm text-gray-800 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-800 dark:text-white/90 dark:focus:border-brand-800">
                </div>

                <!-- Jumlah -->
                <div>
                    <label for="jumlah" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah Masuk</label>
                    <input type="number" id="jumlah" name="jumlah" value="{{ old('jumlah') }}" required min="1"
                           class="w-full rounded-lg border border-gray-200 bg-transparent px-4 py-3 text-sm text-gray-800 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-800 dark:text-white/90 dark:focus:border-brand-800"
                           placeholder="Jumlah stok diterima">
                </div>

                <!-- Harga Beli -->
                <div>
                    <label for="harga_beli" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Harga Beli Total (Rp)</label>
                    <input type="number" id="harga_beli" name="harga_beli" value="{{ old('harga_beli') }}" required min="0" step="100"
                           class="w-full rounded-lg border border-gray-200 bg-transparent px-4 py-3 text-sm text-gray-800 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-800 dark:text-white/90 dark:focus:border-brand-800"
                           placeholder="Contoh: 1500000">
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <button type="reset" class="rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-800/80">
                    Reset
                </button>
                <button type="submit" class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">
                    Simpan Transaksi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
