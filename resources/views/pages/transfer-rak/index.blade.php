@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white/90">Transfer Gudang → Rak</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Riwayat pemindahan stok dari gudang ke display rak menggunakan algoritma FEFO.</p>
            </div>
            <a href="{{ route('transfer-rak.create') }}"
                class="inline-flex items-center gap-1.5 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">
                <i class="ti ti-circle-arrow-right text-base"></i>
                Transfer ke Rak
            </a>
        </div>

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

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-gray-dark">
            <div class="border-b border-gray-100 p-4 sm:p-5 dark:border-gray-800">
                <form action="{{ route('transfer-rak.index') }}" method="GET"
                    class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center">
                        <div class="relative flex-1">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari nama obat, no. batch..."
                                class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50/50 pl-10 pr-4 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900/60 dark:text-white/90 dark:placeholder:text-gray-500 transition duration-150">
                        </div>
                        <div class="w-full sm:w-40">
                            <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}"
                                class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50/50 px-3 text-sm focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900/60 dark:text-white/90 transition duration-150"
                                title="Tanggal Dari">
                        </div>
                        <div class="w-full sm:w-40">
                            <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}"
                                class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50/50 px-3 text-sm focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900/60 dark:text-white/90 transition duration-150"
                                title="Tanggal Sampai">
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="submit" class="h-10 inline-flex items-center justify-center gap-1.5 rounded-lg bg-brand-500 px-4 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-500/30 transition duration-150">Filter</button>
                        @if(request()->hasAny(['search', 'tanggal_dari', 'tanggal_sampai']))
                            <a href="{{ route('transfer-rak.index') }}" class="h-10 inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-3.5 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white transition duration-150" title="Reset Filter">Reset</a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="max-w-full overflow-x-auto custom-scrollbar">
                <table class="w-full min-w-[800px] text-left">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/20">
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">#</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Tgl Transfer</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Nama Obat</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">No. Batch</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">ED Batch</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400 text-center">Jumlah</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Petugas</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transfers as $tr)
                            <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/20">
                                <td class="px-5 py-4 text-sm text-gray-500">
                                    {{ $loop->iteration + ($transfers->currentPage() - 1) * $transfers->perPage() }}
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-800 dark:text-white/90">
                                    {{ $tr->tanggal_transfer->format('d M Y') }}
                                </td>
                                <td class="px-5 py-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                                    {{ $tr->obat->nama_obat ?? '-' }}
                                </td>
                                <td class="px-5 py-4 text-sm font-mono text-gray-600 dark:text-gray-300">
                                    {{ $tr->obatBatch->nomor_batch ?? '-' }}
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $tr->obatBatch ? $tr->obatBatch->tanggal_kadaluwarsa->format('d M Y') : '-' }}
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="font-bold text-brand-600 dark:text-brand-400">{{ $tr->jumlah }}</span>
                                    <span class="text-xs text-gray-400 ml-0.5">{{ $tr->obat->satuan ?? '' }}</span>
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $tr->user->nama_user ?? '-' }}
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $tr->keterangan ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                    Belum ada riwayat transfer ke rak.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($transfers->hasPages())
                <div class="border-t border-gray-100 p-4 dark:border-gray-800">
                    {{ $transfers->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
