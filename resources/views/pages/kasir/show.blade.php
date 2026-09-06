@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white/90">Detail Transaksi</h1>
                <p class="text-sm font-mono text-brand-600 dark:text-brand-400">{{ $penjualan->no_transaksi }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('riwayat-penjualan.index') }}"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                    <i class="ti ti-arrow-left text-base"></i> Kembali
                </a>
                <a href="{{ route('kasir.struk', $penjualan->id) }}" target="_blank"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 shadow-theme-xs transition">
                    <i class="ti ti-printer text-base"></i> Cetak Struk
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            {{-- Info Transaksi --}}
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-gray-dark space-y-3">
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 border-b border-gray-100 dark:border-gray-800 pb-2">Informasi Transaksi</h2>
                @foreach([
                    ['Tanggal', $penjualan->tanggal_transaksi->format('d M Y, H:i')],
                    ['Kasir', $penjualan->user->nama_user ?? '-'],
                    ['Pembeli', $penjualan->nama_pembeli ?? 'Umum'],
                    ['Total Omzet (Penjualan)', 'Rp ' . number_format($penjualan->total_harga, 0, ',', '.')],
                    ['Total Modal (HPP)', 'Rp ' . number_format($penjualan->total_hpp, 0, ',', '.')],
                    ['Laba / Untung Bersih', '+Rp ' . number_format($penjualan->total_laba, 0, ',', '.')],
                    ['Nominal Bayar', 'Rp ' . number_format($penjualan->nominal_bayar, 0, ',', '.')],
                    ['Kembalian', 'Rp ' . number_format($penjualan->kembalian, 0, ',', '.')],
                ] as [$label, $value])
                    <div class="flex justify-between text-sm {{ str_contains($label, 'Laba') ? 'bg-success-50 dark:bg-success-900/20 p-2 rounded-lg text-success-700 dark:text-success-300 font-bold' : '' }}">
                        <span class="{{ str_contains($label, 'Laba') ? 'text-success-700 dark:text-success-300' : 'text-gray-500 dark:text-gray-400' }}">{{ $label }}</span>
                        <span class="font-semibold {{ str_contains($label, 'Laba') ? 'text-success-700 dark:text-success-300 font-bold' : 'text-gray-800 dark:text-white/90' }}">{{ $value }}</span>
                    </div>
                @endforeach
                @if($penjualan->catatan)
                    <div class="rounded-lg bg-gray-50 dark:bg-gray-900/40 px-3 py-2 text-xs text-gray-500 dark:text-gray-400">
                        📝 {{ $penjualan->catatan }}
                    </div>
                @endif
            </div>

            {{-- Detail Item --}}
            <div class="rounded-xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-gray-dark overflow-hidden">
                <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-800">
                    <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Detail Item ({{ $penjualan->details->count() }} item)</h2>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach($penjualan->details as $detail)
                        <div class="px-5 py-3 flex items-start justify-between text-sm">
                            <div>
                                <div class="font-semibold text-gray-800 dark:text-white/90">{{ $detail->obat->nama_obat ?? '-' }}</div>
                                <div class="text-xs text-gray-400 mt-0.5">
                                    {{ $detail->jumlah }} {{ $detail->obat->satuan_jual ?? '' }} × Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}
                                </div>
                                <div class="text-xs text-gray-400">
                                    HPP: Rp {{ number_format($detail->hpp_satuan, 0, ',', '.') }}/{{ $detail->obat->satuan_jual ?? '' }}
                                    (Modal: Rp {{ number_format($detail->total_hpp, 0, ',', '.') }})
                                </div>
                                <div class="text-xs text-gray-300 dark:text-gray-600">
                                    Batch: {{ $detail->obatBatch->nomor_batch ?? '-' }}
                                    | ED: {{ $detail->obatBatch ? $detail->obatBatch->tanggal_kadaluwarsa->format('d/m/Y') : '-' }}
                                </div>
                            </div>
                            <div class="text-right shrink-0 ml-4">
                                <div class="font-bold text-gray-800 dark:text-white/90">
                                    Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                                </div>
                                <div class="text-xs font-semibold text-success-600 dark:text-success-400">
                                    +Rp {{ number_format($detail->laba, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
