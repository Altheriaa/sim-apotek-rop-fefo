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

    <!-- Table Card with Integrated Toolbar -->
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-gray-dark">
        <!-- Toolbar / Filter Header -->
        <div class="border-b border-gray-100 p-4 sm:p-5 dark:border-gray-800">
            <form action="{{ route('obat-keluar.index') }}" method="GET" class="flex flex-col gap-3.5 sm:flex-row sm:items-center sm:justify-between">
                <div class="grid flex-1 grid-cols-1 gap-3 sm:grid-cols-3">
                    <!-- Search Input -->
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400 dark:text-gray-500">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>  
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari obat, no batch, petugas..."
                               class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50/50 pl-10 pr-4 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900/60 dark:text-white/90 dark:placeholder:text-gray-500 dark:focus:border-brand-500 dark:focus:bg-gray-900 transition duration-150">
                    </div>

                    <!-- Tanggal Dari -->
                    <div>
                        <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}"
                               class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50/50 px-3.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900/60 dark:text-white/90 dark:focus:border-brand-500 dark:focus:bg-gray-900 transition duration-150"
                               title="Tanggal Keluar Dari">
                    </div>

                    <!-- Tanggal Sampai -->
                    <div>
                        <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}"
                               class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50/50 px-3.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900/60 dark:text-white/90 dark:focus:border-brand-500 dark:focus:bg-gray-900 transition duration-150"
                               title="Tanggal Keluar Sampai">
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="h-10 inline-flex items-center justify-center gap-1.5 rounded-lg bg-brand-500 px-4 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-500/30 transition duration-150">
                        Filter Data
                    </button>
                    @if(request()->hasAny(['search', 'tanggal_dari', 'tanggal_sampai']))
                    <a href="{{ route('obat-keluar.index') }}" class="h-10 inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-3.5 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white transition duration-150" title="Reset Filter">
                        Reset
                    </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="max-w-full overflow-x-auto custom-scrollbar">
            <table class="w-full min-w-[850px] text-left">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/20">
                        <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">#</th>
                        <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal</th>
                        <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Obat</th>
                        <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">No. Batch (FEFO)</th>
                        <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Tgl. ED Batch</th>
                        <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Jumlah Keluar</th>
                        <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Petugas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($obatKeluars as $trx)
                        <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/20">
                            <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $loop->iteration + ($obatKeluars->currentPage() - 1) * $obatKeluars->perPage() }}
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-800 dark:text-white/90">
                                {{ \Carbon\Carbon::parse($trx->tanggal_keluar)->format('d M Y') }}
                            </td>
                            <td class="px-5 py-4 text-sm font-medium text-gray-800 dark:text-white/90">
                                {{ $trx->obat->nama_obat ?? '-' }}
                            </td>
                            <td class="px-5 py-4 text-sm text-brand-600 dark:text-brand-400 font-mono font-medium">
                                {{ $trx->obatBatch->nomor_batch ?? '-' }}
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $trx->obatBatch ? \Carbon\Carbon::parse($trx->obatBatch->tanggal_kadaluwarsa)->format('d M Y') : '-' }}
                            </td>
                            <td class="px-5 py-4 text-sm font-bold text-error-600 dark:text-error-400">
                                -{{ $trx->jumlah }} {{ $trx->obat->satuan ?? '' }}
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $trx->user->nama_user ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                @if(request()->hasAny(['search', 'tanggal_dari', 'tanggal_sampai']))
                                    Tidak ada transaksi obat keluar yang sesuai dengan filter.
                                @else
                                    Belum ada transaksi obat keluar.
                                @endif
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
