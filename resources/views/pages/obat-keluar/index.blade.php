@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white/90">Transaksi Obat Keluar</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Pencatatan obat yang dikeluarkan (Otomatis menggunakan metode FEFO).</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('obat-keluar.create') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Catat Obat Keluar
            </a>
        </div>
    </div>

    <!-- Alert Success/Error -->
    @if(session('success'))
    <div class="rounded-lg bg-success-50 p-4 text-success-800 border border-success-200 dark:bg-success-900/20 dark:text-success-400 dark:border-success-800/30">
        {{ session('success') }}
    </div>
    @endif
    
    @if(session('error'))
    <div class="rounded-lg bg-error-50 p-4 text-error-800 border border-error-200 dark:bg-error-900/20 dark:text-error-400 dark:border-error-800/30">
        {{ session('error') }}
    </div>
    @endif

    <!-- Table -->
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="max-w-full overflow-x-auto custom-scrollbar">
            <table class="w-full min-w-[1000px] text-left">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/20">
                        <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal</th>
                        <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Obat</th>
                        <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">No. Batch (FEFO)</th>
                        <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Tgl. ED</th>
                        <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Jumlah Keluar</th>
                        <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Petugas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($obatKeluars as $trx)
                        <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/20">
                            <td class="px-5 py-4 text-sm text-gray-800 dark:text-white/90">
                                {{ \Carbon\Carbon::parse($trx->tanggal_keluar)->format('d M Y') }}
                            </td>
                            <td class="px-5 py-4 text-sm font-medium text-gray-800 dark:text-white/90">
                                {{ $trx->obat->nama_obat }}
                            </td>
                            <td class="px-5 py-4 text-sm text-brand-600 dark:text-brand-400 font-medium">
                                {{ $trx->obatBatch->nomor_batch ?? '-' }}
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $trx->obatBatch ? \Carbon\Carbon::parse($trx->obatBatch->tanggal_kadaluwarsa)->format('d M Y') : '-' }}
                            </td>
                            <td class="px-5 py-4 text-sm font-bold text-error-600 dark:text-error-400">
                                -{{ $trx->jumlah }}
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $trx->user->nama_user }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                Belum ada transaksi obat keluar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($obatKeluars->hasPages())
        <div class="border-t border-gray-100 p-4 dark:border-gray-800">
            {{ $obatKeluars->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
