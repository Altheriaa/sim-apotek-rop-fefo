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
            <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800">
                <h3 class="font-semibold text-gray-800 dark:text-white/90">Notifikasi Terbaru</h3>
            </div>
            <div class="p-5">
                @if($notifikasiTerbaru->isEmpty())
                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada notifikasi.</p>
                @else
                    <ul class="space-y-4">
                        @foreach($notifikasiTerbaru as $notif)
                            <li class="flex items-start gap-3 border-b border-gray-100 pb-3 last:border-0 last:pb-0 dark:border-gray-800">
                                @if($notif->tipe === 'rop')
                                    <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-warning-100 text-warning-600 dark:bg-warning-900/20 dark:text-warning-400">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    </div>
                                @else
                                    <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-error-100 text-error-600 dark:bg-error-900/20 dark:text-error-400">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                @endif
                                <div>
                                    <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $notif->pesan }}</p>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $notif->created_at->diffForHumans() }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <!-- Aktivitas Terakhir (Obat Keluar) -->
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-dark">
            <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800 flex justify-between items-center">
                <h3 class="font-semibold text-gray-800 dark:text-white/90">Aktivitas Obat Keluar</h3>
                <a href="{{ route('obat-keluar.index') }}" class="text-sm font-medium text-brand-500 hover:text-brand-600">Lihat Semua</a>
            </div>
            <div class="p-5">
                 @if($aktivitasTerakhir->isEmpty())
                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada aktivitas obat keluar.</p>
                @else
                    <ul class="space-y-4">
                        @foreach($aktivitasTerakhir->take(5) as $aktivitas)
                            <li class="flex items-center justify-between border-b border-gray-100 pb-3 last:border-0 last:pb-0 dark:border-gray-800">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
                                        <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $aktivitas->obat->nama_obat }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Batch: {{ $aktivitas->obatBatch->nomor_batch ?? '-' }} • Oleh: {{ $aktivitas->user->nama_user }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-bold text-gray-800 dark:text-white/90">-{{ $aktivitas->jumlah }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($aktivitas->tanggal_keluar)->format('d M Y') }}</p>
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
