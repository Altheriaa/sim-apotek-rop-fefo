@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('obat-masuk.index') }}"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <i class="ti ti-arrow-left text-xl"></i>
                    </a>
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white/90">Detail Transaksi Obat Masuk</h1>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 ml-7">Rincian penerimaan obat, data supplier, harga modal
                    (HPP), dan status stok batch.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('obat-masuk.index') }}"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition">
                    <i class="ti ti-arrow-left"></i> Kembali ke Daftar
                </a>
            </div>
        </div>

        @php
            $isi = max(1, (int) ($batch->obat->isi_per_kemasan ?? 1));
            $modalPerJual = $isi > 0 ? ((float) $batch->harga_beli_satuan / $isi) : 0;
            $ed = \Carbon\Carbon::parse($batch->tanggal_kadaluwarsa);
            $isExpired = $ed->isPast();
            $isNear = !$isExpired && $ed->lte(now()->addDays(30));
            $hargaJual = (float) ($batch->obat->harga_jual ?? 0);
            $marginPerJual = $hargaJual - $modalPerJual;
            $marginPersen = $hargaJual > 0 ? round(($marginPerJual / $hargaJual) * 100, 1) : 0;
        @endphp

        <!-- Top Summary Metric Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Total Faktur Pembelian -->
            <div
                class="rounded-2xl border border-brand-200 bg-brand-50/60 p-4 dark:border-brand-900/30 dark:bg-brand-900/10 flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-brand-600 dark:text-brand-400">Total Faktur Pembelian</p>
                    <h4 class="mt-1 text-xl font-bold text-brand-700 dark:text-brand-300">Rp
                        {{ number_format($batch->harga_beli, 0, ',', '.') }}</h4>
                </div>
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-100 text-brand-600 dark:bg-brand-900/40 dark:text-brand-400">
                    <i class="ti ti-receipt text-xl"></i>
                </div>
            </div>

            <!-- Harga Beli per Satuan Beli -->
            <div
                class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-dark flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Harga Modal per
                        {{ $batch->obat->satuan_beli }}</p>
                    <h4 class="mt-1 text-xl font-bold text-gray-800 dark:text-white/90">Rp
                        {{ number_format($batch->harga_beli_satuan, 0, ',', '.') }}</h4>
                </div>
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                    <i class="ti ti-box text-xl"></i>
                </div>
            </div>

            <!-- Modal Pokok (HPP) per Satuan Jual -->
            <div
                class="rounded-2xl border border-blue-200 bg-blue-50/60 p-4 dark:border-blue-900/30 dark:bg-blue-900/10 flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-blue-600 dark:text-blue-400">Modal Pokok (HPP) /
                        {{ $batch->obat->satuan_jual }}</p>
                    <h4 class="mt-1 text-xl font-bold text-blue-700 dark:text-blue-300">Rp
                        {{ number_format($modalPerJual, 0, ',', '.') }}</h4>
                </div>
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-400">
                    <i class="ti ti-coin text-xl"></i>
                </div>
            </div>

            <!-- Estimasi Margin Untung -->
            <div
                class="rounded-2xl border border-success-200 bg-success-50/60 p-4 dark:border-success-900/30 dark:bg-success-900/10 flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-1">
                        <p class="text-xs font-medium text-success-700 dark:text-success-400">Margin /
                            {{ $batch->obat->satuan_jual }}</p>
                        <span
                            class="rounded-full bg-success-200 px-1.5 py-0.2 text-[10px] font-bold text-success-800 dark:bg-success-800 dark:text-success-200">+{{ $marginPersen }}%</span>
                    </div>
                    <h4 class="mt-1 text-xl font-bold text-success-700 dark:text-success-300">+Rp
                        {{ number_format($marginPerJual, 0, ',', '.') }}</h4>
                </div>
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-full bg-success-100 text-success-700 dark:bg-success-900/40 dark:text-success-300">
                    <i class="ti ti-trending-up text-xl"></i>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            {{-- Panel 1: Data Penerimaan & Batch --}}
            <div
                class="rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-gray-dark space-y-4">
                <div class="border-b border-gray-100 pb-3 dark:border-gray-800 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-gray-800 dark:text-white/90">Informasi Batch & Penerimaan</h2>
                    <span
                        class="inline-flex items-center font-mono font-bold text-xs bg-brand-50 text-brand-600 dark:bg-brand-900/30 dark:text-brand-400 px-2.5 py-1 rounded-md">
                        {{ $batch->nomor_batch }}
                    </span>
                </div>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between py-1 border-b border-gray-50 dark:border-gray-800/60">
                        <span class="text-gray-500 dark:text-gray-400">Nama Obat</span>
                        <span class="font-bold text-gray-800 dark:text-white/90">{{ $batch->obat->nama_obat }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-gray-50 dark:border-gray-800/60">
                        <span class="text-gray-500 dark:text-gray-400">Kode & Kategori</span>
                        <span class="font-medium text-gray-700 dark:text-gray-300">{{ $batch->obat->kode_obat }} -
                            {{ $batch->obat->kategori }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-gray-50 dark:border-gray-800/60">
                        <span class="text-gray-500 dark:text-gray-400">Supplier</span>
                        <span
                            class="font-semibold text-gray-800 dark:text-white/90">{{ $batch->supplier->nama_supplier ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-gray-50 dark:border-gray-800/60">
                        <span class="text-gray-500 dark:text-gray-400">Kontak Supplier</span>
                        <span class="text-gray-700 dark:text-gray-300">{{ $batch->supplier->kontak ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-gray-50 dark:border-gray-800/60">
                        <span class="text-gray-500 dark:text-gray-400">Alamat Supplier</span>
                        <span
                            class="text-right text-gray-700 dark:text-gray-300 max-w-[280px]">{{ $batch->supplier->alamat ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-gray-50 dark:border-gray-800/60">
                        <span class="text-gray-500 dark:text-gray-400">Tanggal Masuk (Terima)</span>
                        <span
                            class="font-semibold text-gray-800 dark:text-white/90">{{ \Carbon\Carbon::parse($batch->tanggal_masuk)->format('d F Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-1">
                        <span class="text-gray-500 dark:text-gray-400">Tanggal Kadaluwarsa (ED)</span>
                        <span
                            class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $isExpired ? 'bg-error-100 text-error-700 dark:bg-error-900/30 dark:text-error-400' : ($isNear ? 'bg-warning-100 text-warning-700 dark:bg-warning-900/30 dark:text-warning-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300') }}">
                            {{ $ed->format('d M Y') }} ({{ $ed->diffForHumans() }})
                        </span>
                    </div>
                </div>

                {{-- Status Stok Saat Ini --}}
                <div
                    class="rounded-xl bg-gray-50 dark:bg-gray-900/40 p-4 border border-gray-100 dark:border-gray-800 space-y-2.5">
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Status Stok Batch Saat Ini</h3>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Tersimpan di Gudang Fisik</span>
                        <span class="font-bold text-blue-600 dark:text-blue-400">{{ $batch->stok_gudang }}
                            {{ $batch->obat->satuan_beli }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Telah Ditransfer ke Rak Display Kasir</span>
                        <span class="font-bold text-green-600 dark:text-green-400">{{ $batch->stok_rak }}
                            {{ $batch->obat->satuan_jual }}</span>
                    </div>
                </div>
            </div>

            {{-- Panel 2: Rincian Pembelian & Kalkulasi Modal (HPP) --}}
            <div
                class="rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-gray-dark space-y-4">
                <div class="border-b border-gray-100 pb-3 dark:border-gray-800">
                    <h2 class="text-sm font-bold text-gray-800 dark:text-white/90">Rincian Pembelian & Perhitungan Modal
                        (HPP)</h2>
                </div>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between py-1 border-b border-gray-50 dark:border-gray-800/60">
                        <span class="text-gray-500 dark:text-gray-400">Harga Beli Faktur /
                            {{ $batch->obat->satuan_beli }}</span>
                        <span class="font-bold text-gray-800 dark:text-white/90">Rp
                            {{ number_format($batch->harga_beli_satuan, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-gray-50 dark:border-gray-800/60">
                        <span class="text-gray-500 dark:text-gray-400">Konversi Kemasan</span>
                        <span class="font-medium text-gray-700 dark:text-gray-300">1 {{ $batch->obat->satuan_beli }} =
                            {{ $isi }} {{ $batch->obat->satuan_jual }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-gray-50 dark:border-gray-800/60">
                        <span class="font-semibold text-brand-600 dark:text-brand-400">Modal Pokok (HPP) /
                            {{ $batch->obat->satuan_jual }}</span>
                        <span class="font-bold text-brand-600 dark:text-brand-400">Rp
                            {{ number_format($modalPerJual, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-gray-50 dark:border-gray-800/60">
                        <span class="text-gray-500 dark:text-gray-400">Harga Jual ke Pasien /
                            {{ $batch->obat->satuan_jual }}</span>
                        <span class="font-bold text-gray-800 dark:text-white/90">Rp
                            {{ number_format($hargaJual, 0, ',', '.') }}</span>
                    </div>
                    <div
                        class="flex justify-between items-center bg-success-50 dark:bg-success-900/20 p-2.5 rounded-lg text-success-700 dark:text-success-300 font-bold">
                        <span>Estimasi Keuntungan Bersih / {{ $batch->obat->satuan_jual }}</span>
                        <span>+Rp {{ number_format($marginPerJual, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between pt-2">
                        <span class="text-base font-medium text-gray-700 dark:text-gray-300">Total Faktur Pembelian
                            Batch</span>
                        <span class="text-xl font-bold text-brand-600 dark:text-brand-400">Rp
                            {{ number_format($batch->harga_beli, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection