@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white/90">Tambah Data Obat</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Masukkan informasi detail obat baru.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('obat.index') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-800/80">
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
        <form action="{{ route('obat.store') }}" method="POST" class="p-6">
            @csrf
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <!-- Kode Obat -->
                <div>
                    <label for="kode_obat" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Kode Obat</label>
                    <input type="text" id="kode_obat" name="kode_obat" value="{{ old('kode_obat') }}" required
                           class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-gray-500 dark:focus:border-brand-500 transition duration-150"
                           placeholder="Contoh: OB001">
                </div>

                <!-- Nama Obat -->
                <div>
                    <label for="nama_obat" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Obat</label>
                    <input type="text" id="nama_obat" name="nama_obat" value="{{ old('nama_obat') }}" required
                           class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-gray-500 dark:focus:border-brand-500 transition duration-150"
                           placeholder="Masukkan nama obat">
                </div>

                <!-- Kategori -->
                <div>
                    <label for="kategori" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Kategori</label>
                    <input type="text" id="kategori" name="kategori" value="{{ old('kategori') }}" required
                           class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-gray-500 dark:focus:border-brand-500 transition duration-150"
                           placeholder="Contoh: Tablet, Sirup, Injeksi">
                </div>

                <!-- Satuan -->
                <div>
                    <label for="satuan" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Satuan</label>
                    <input type="text" id="satuan" name="satuan" value="{{ old('satuan') }}" required
                           class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-gray-500 dark:focus:border-brand-500 transition duration-150"
                           placeholder="Contoh: Pcs, Box, Botol">
                </div>

                <!-- Harga -->
                <div>
                    <label for="harga" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Harga (Rp)</label>
                    <input type="number" id="harga" name="harga" value="{{ old('harga') }}" required min="0" step="100"
                           class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-gray-500 dark:focus:border-brand-500 transition duration-150"
                           placeholder="Contoh: 15000">
                </div>

                <!-- ROP Minimum -->
                <div>
                    <label for="rop_minimum" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Batas Minimal (ROP)</label>
                    <input type="number" id="rop_minimum" name="rop_minimum" value="{{ old('rop_minimum', 10) }}" required min="0"
                           class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-gray-500 dark:focus:border-brand-500 transition duration-150"
                           placeholder="Minimal stok sebelum dipesan">
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <button type="reset" class="h-11 inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-800/80 transition duration-150">
                    Reset
                </button>
                <button type="submit" class="h-11 inline-flex items-center justify-center rounded-lg bg-brand-500 px-6 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-500/30 transition duration-150">
                    Simpan Obat
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
