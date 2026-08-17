@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white/90">Dashboard Apotek</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Sistem Informasi Manajemen Stok Obat - FEFO & ROP</p>
        </div>
        <div class="flex gap-2">
            <span class="inline-flex items-center gap-1.5 rounded-lg bg-white px-3 py-2 text-sm font-medium text-gray-700 dark:bg-gray-800 dark:text-gray-300 border border-gray-200 dark:border-gray-700">
                <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                {{ now()->isoFormat('D MMMM Y') }}
            </span>
        </div>
    </div>

    <!-- Metrics -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 lg:gap-6">
        <!-- Card 1 -->
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-dark">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Jenis Obat</p>
                    <h4 class="mt-1 text-2xl font-bold text-gray-800 dark:text-white/90">{{ number_format($totalObat) }}</h4>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-full bg-brand-100 text-brand-600 dark:bg-brand-900/20 dark:text-brand-400">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-dark">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Stok Fisik</p>
                    <h4 class="mt-1 text-2xl font-bold text-gray-800 dark:text-white/90">{{ number_format($totalStok) }}</h4>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-full bg-success-100 text-success-600 dark:bg-success-900/20 dark:text-success-400">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="rounded-2xl border border-warning-200 bg-warning-50 p-5 dark:border-warning-800/30 dark:bg-warning-900/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-warning-600 dark:text-warning-400">Obat Kritis ( < ROP)</p>
                    <h4 class="mt-1 text-2xl font-bold text-warning-700 dark:text-warning-300">{{ number_format($obatKritisCount) }}</h4>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-full bg-warning-200 text-warning-700 dark:bg-warning-800/50 dark:text-warning-300">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="rounded-2xl border border-error-200 bg-error-50 p-5  dark:border-error-800/30 dark:bg-error-900/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-error-600 dark:text-error-400">Mendekati ED (< 30 Hari)</p>
                    <h4 class="mt-1 text-2xl font-bold text-error-700 dark:text-error-300">{{ number_format($batchEdCount) }}</h4>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-full bg-error-200 text-error-700 dark:bg-error-800/50 dark:text-error-300">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 lg:gap-6">
        <!-- Notifikasi Terbaru -->
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-dark">
            <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800 flex justify-between items-center">
                <h3 class="font-semibold text-gray-800 dark:text-white/90">Notifikasi Terbaru</h3>
                <span class="text-xs font-semibold px-2 py-0.5 dark:text-white">
                    {{ $notifikasiTerbaru->count() }} Terkini
                </span>
            </div>
            <div class="p-5">
                @if($notifikasiTerbaru->isEmpty())
                    <div class="flex flex-col items-center justify-center py-6 text-gray-400 text-center">
                        <i class="ti ti-bell-off text-3xl mb-1 text-gray-300 dark:text-gray-600"></i>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada notifikasi baru.</p>
                    </div>
                @else
                    <ul class="space-y-3">
                        @foreach($notifikasiTerbaru as $notif)
                            @php
                                $isRop = $notif->jenis_notifikasi === 'stok_menipis';
                                $isRak = $notif->jenis_notifikasi === 'restock_rak';
                                $isEd  = in_array($notif->jenis_notifikasi, ['mendekati_kadaluwarsa', 'kadaluwarsa']);
                            @endphp
                            <li class="flex items-start gap-3 rounded-xl border border-gray-100 p-3.5 dark:border-gray-800/80 dark:bg-gray-900/40 hover:border-gray-200 transition">
                                @if($isRop)
                                    <div class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-warning-600/15  text-warning-600 dark:bg-warning-900/30 dark:text-warning-400">
                                        <i class="ti ti-alert-triangle text-lg"></i>
                                    </div>
                                @elseif($isRak)
                                    <div class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-dark-600 dark:text-white-400">
                                        <i class="ti ti-arrow-right-circle text-lg"></i>
                                    </div>
                                @else
                                    <div class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-error-50 text-error-600 dark:text-white-400">
                                        <i class="ti ti-clock-alert text-lg"></i>
                                    </div>
                                @endif

                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between gap-2 mb-1">
                                        <span class="inline-flex items-center text-xs font-bold {{ $isRop ? 'text-warning-700 dark:text-warning-400' : ($isRak ? 'text-blue-700 dark:text-blue-400' : 'text-error-700 dark:text-error-400') }}">
                                            {{ $notif->judul }}
                                        </span>
                                        <span class="text-[11px] text-gray-400 shrink-0">
                                            {{ $notif->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                    
                                    <div class="text-xs text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">
                                        {{ $notif->pesan_rapi }}
                                    </div>

                                    @if($notif->obat)
                                        <div class="mt-2 flex items-center gap-2">
                                            @if($isRop)
                                                <a href="{{ route('pesanan.create', ['obat_id' => $notif->obat_id]) }}"
                                                    class="inline-flex items-center gap-1 text-[11px] font-semibold text-warning-700 hover:text-warning-800 dark:text-warning-400 hover:underline">
                                                    <i class="ti ti-shopping-cart-plus"></i> Buat Pesanan Supplier
                                                </a>
                                            @elseif($isRak)
                                                <a href="{{ route('transfer-rak.create', ['obat_id' => $notif->obat_id]) }}"
                                                    class="inline-flex items-center gap-1 text-[11px] font-semibold text-blue-700 hover:text-blue-800 dark:text-blue-400 hover:underline">
                                                    <i class="ti ti-arrow-right"></i> Transfer ke Rak
                                                </a>
                                            @else
                                                <a href="{{ route('stok-gudang.index', ['search' => $notif->obat->nama_obat]) }}"
                                                    class="inline-flex items-center gap-1 text-[11px] font-semibold text-error-700 hover:text-error-800 dark:text-error-400 hover:underline">
                                                    <i class="ti ti-eye"></i> Cek Stok & ED
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <!-- Aktivitas Transfer Rak Terbaru -->
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-dark">
            <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800 flex justify-between items-center">
                <h3 class="font-semibold text-gray-800 dark:text-white/90">Transfer Rak Terbaru</h3>
                <a href="{{ route('transfer-rak.index') }}" class="text-sm font-medium text-black dark:text-white/90">Lihat Semua</a>
            </div>
            <div class="p-5">
                 @if($transferTerakhir->isEmpty())
                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada aktivitas transfer ke rak.</p>
                @else
                    <ul class="space-y-4">
                        @foreach($transferTerakhir->take(5) as $tr)
                            <li class="flex items-center justify-between border-b border-gray-100 pb-3 last:border-0 last:pb-0 dark:border-gray-800">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-ful">
                                        <i class="ti ti-circle-arrow-right text-xl text-black dark:text-white/90"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $tr->obat->nama_obat }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Oleh: {{ $tr->user->nama_user ?? '-' }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-bold text-brand-600 dark:text-brand-400">+{{ $tr->jumlah }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($tr->tanggal_transfer)->format('d M Y') }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
