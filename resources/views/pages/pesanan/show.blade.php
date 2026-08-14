@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white/90">Detail Pesanan</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Informasi detail pesanan dan pembaruan status.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('pesanan.index') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-800/80">
                Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="rounded-lg bg-success-50 p-4 text-success-800 border border-success-200 dark:bg-success-900/20 dark:text-success-400 dark:border-success-800/30">
        {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Informasi Pesanan -->
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-sm dark:border-gray-800 dark:bg-gray-dark">
                <h3 class="mb-5 text-lg font-bold text-gray-800 dark:text-white/90">Informasi Utama</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Tanggal Pesan</p>
                        <p class="font-medium text-gray-800 dark:text-white/90">{{ \Carbon\Carbon::parse($pesanan->tanggal_pesan)->format('d F Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Status Saat Ini</p>
                        @php
                            $statusClass = [
                                'draft' => 'text-gray-700',
                                'diproses' => 'text-warning-600',
                                'dikirim' => 'text-blue-600',
                                'selesai' => 'text-success-600',
                                'batal' => 'text-error-600',
                            ];
                            $class = $statusClass[$pesanan->status] ?? 'text-gray-700';
                        @endphp
                        <p class="font-bold {{ $class }}">{{ strtoupper($pesanan->status) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Obat</p>
                        <p class="font-medium text-gray-800 dark:text-white/90">{{ $pesanan->obat->nama_obat }} ({{ $pesanan->obat->kode_obat }})</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Supplier Tujuan</p>
                        <p class="font-medium text-gray-800 dark:text-white/90">{{ $pesanan->supplier->nama_supplier ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Jumlah Dipesan</p>
                        <p class="font-medium text-gray-800 dark:text-white/90">{{ $pesanan->jumlah_pesan }} {{ $pesanan->obat->satuan }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Estimasi Total Harga</p>
                        <p class="font-medium text-gray-800 dark:text-white/90">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-sm dark:border-gray-800 dark:bg-gray-dark">
                <h3 class="mb-5 text-lg font-bold text-gray-800 dark:text-white/90">Catatan/Keterangan</h3>
                <p class="text-gray-600 dark:text-gray-400">{{ $pesanan->keterangan ?: 'Tidak ada catatan khusus untuk pesanan ini.' }}</p>
            </div>
        </div>

        <!-- Update Status Form -->
        <div class="lg:col-span-1">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-sm dark:border-gray-800 dark:bg-gray-dark">
                <h3 class="mb-5 text-lg font-bold text-gray-800 dark:text-white/90">Update Status</h3>
                
                @if($pesanan->status == 'selesai' || $pesanan->status == 'batal')
                    <div class="rounded-lg bg-gray-50 p-4 border border-gray-200 dark:bg-gray-800/50 dark:border-gray-700">
                        <p class="text-sm text-gray-600 dark:text-gray-400 text-center">Pesanan ini sudah <b>{{ $pesanan->status }}</b> dan tidak dapat diubah lagi.</p>
                    </div>
                @else
                    <form action="{{ route('pesanan.updateStatus', $pesanan->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="space-y-4">
                            <div>
                                <label for="status" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Pilih Status Baru</label>
                                <div class="relative">
                                    <select id="status" name="status" required
                                            class="h-11 w-full appearance-none rounded-lg border border-gray-200 bg-transparent px-4 py-2.5 pr-10 text-sm text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-500 transition duration-150 cursor-pointer">
                                        <option value="draft" {{ $pesanan->status == 'draft' ? 'selected' : '' }} class="dark:bg-gray-900">Draft</option>
                                        <option value="diproses" {{ $pesanan->status == 'diproses' ? 'selected' : '' }} class="dark:bg-gray-900">Diproses Supplier</option>
                                        <option value="dikirim" {{ $pesanan->status == 'dikirim' ? 'selected' : '' }} class="dark:bg-gray-900">Dikirim (Dalam Perjalanan)</option>
                                        <option value="batal" {{ $pesanan->status == 'batal' ? 'selected' : '' }} class="dark:bg-gray-900">Batal</option>
                                    </select>
                                    <span class="pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="pt-2">
                                <button type="submit" class="h-11 w-full inline-flex items-center justify-center rounded-lg bg-brand-500 px-5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-500/30 transition duration-150">
                                    Simpan Perubahan
                                </button>
                            </div>
                            
                            <hr class="border-gray-100 dark:border-gray-800 my-4" />
                            
                            <div class="rounded-lg bg-blue-50 p-4 border border-blue-200 dark:bg-blue-900/20 dark:border-blue-800/30">
                                <p class="text-xs text-blue-700 dark:text-blue-400">
                                    <b>Info:</b> Untuk mengubah status menjadi <b>Selesai</b> (barang diterima), silakan catat penerimaan barang di menu <b>Obat Masuk</b>.
                                </p>
                            </div>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
