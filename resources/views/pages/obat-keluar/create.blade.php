@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white/90">Catat Obat Keluar</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Sistem otomatis menggunakan metode FEFO (First Expired First Out) untuk memilih batch.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('obat-keluar.index') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-800/80">
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
    
    @if(session('error'))
    <div class="rounded-lg bg-error-50 p-4 text-error-800 border border-error-200 dark:bg-error-900/20 dark:text-error-400 dark:border-error-800/30">
        {{ session('error') }}
    </div>
    @endif

    <div class="rounded-2xl border border-gray-200 bg-white shadow-theme-sm dark:border-gray-800 dark:bg-gray-dark">
        <form action="{{ route('obat-keluar.store') }}" method="POST" class="p-6">
            @csrf
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <!-- Obat -->
                <div>
                    <label for="obat_id" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Pilih Obat (Stok Tersedia)</label>
                    <select id="obat_id" name="obat_id" required
                            class="w-full rounded-lg border border-gray-200 bg-transparent px-4 py-3 text-sm text-gray-800 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-800 dark:text-white/90 dark:focus:border-brand-800 dark:bg-gray-900">
                        <option value="" disabled selected>Pilih Obat...</option>
                        @foreach($obats as $obat)
                            <option value="{{ $obat->id }}" {{ old('obat_id') == $obat->id ? 'selected' : '' }}>
                                {{ $obat->kode_obat }} - {{ $obat->nama_obat }} (Stok: {{ $obat->stok_total }} {{ $obat->satuan }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Jumlah -->
                <div>
                    <label for="jumlah" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah Keluar</label>
                    <input type="number" id="jumlah" name="jumlah" value="{{ old('jumlah') }}" required min="1"
                           class="w-full rounded-lg border border-gray-200 bg-transparent px-4 py-3 text-sm text-gray-800 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-800 dark:text-white/90 dark:focus:border-brand-800"
                           placeholder="Masukkan jumlah yang dikeluarkan">
                </div>

                <!-- Keterangan -->
                <div class="sm:col-span-2">
                    <label for="keterangan" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Keterangan / Tujuan Keluar</label>
                    <textarea id="keterangan" name="keterangan" rows="3" required
                              class="w-full rounded-lg border border-gray-200 bg-transparent px-4 py-3 text-sm text-gray-800 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-800 dark:text-white/90 dark:focus:border-brand-800"
                              placeholder="Contoh: Permintaan Poli Umum, Resep Pasien, dll">{{ old('keterangan') }}</textarea>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <button type="reset" class="rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-800/80">
                    Reset
                </button>
                <button type="submit" class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">
                    Proses Pengeluaran (FEFO)
                </button>
            </div>
        </form>
    </div>
    
    <div class="mt-6 rounded-lg bg-blue-50 p-4 border border-blue-200 dark:bg-blue-900/20 dark:border-blue-800/30">
        <div class="flex gap-3">
            <svg class="h-6 w-6 shrink-0 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <h3 class="text-sm font-medium text-blue-800 dark:text-blue-300">Informasi FEFO</h3>
                <p class="mt-1 text-sm text-blue-700 dark:text-blue-400">
                    Sistem akan secara otomatis mengurangi stok dari batch obat yang memiliki <b>Tanggal Kadaluwarsa paling dekat</b>. 
                    Jika stok pada batch tersebut tidak cukup, sistem akan otomatis mengambil sisa kebutuhan dari batch terdekat selanjutnya.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
