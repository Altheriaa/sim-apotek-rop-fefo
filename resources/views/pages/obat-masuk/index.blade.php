@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white/90">Transaksi Obat Masuk</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Pencatatan penerimaan obat dari supplier dan pengaturan
                    batch / ED.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('obat-masuk.create') }}"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Catat Obat Masuk
                </a>
            </div>
        </div>

        <!-- Alert Success -->
        @if(session('success'))
            <div
                class="rounded-lg bg-success-50 p-4 text-success-800 border border-success-200 dark:bg-success-900/20 dark:text-success-400 dark:border-success-800/30">
                {{ session('success') }}
            </div>
        @endif

        <!-- Table Card with Integrated Toolbar -->
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-gray-dark">
            <!-- Toolbar / Filter Header -->
            <div class="border-b border-gray-100 p-4 sm:p-5 dark:border-gray-800">
                <form action="{{ route('obat-masuk.index') }}" method="GET"
                    class="flex flex-col gap-3 xl:flex-row xl:items-center xl:justify-between">
                    <div class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center flex-wrap">
                        <!-- Search Input -->
                        <div class="relative flex-1 min-w-[200px]">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400 dark:text-gray-500">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari obat, no batch, supplier..."
                                class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50/50 pl-10 pr-4 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900/60 dark:text-white/90 dark:placeholder:text-gray-500 dark:focus:border-brand-500 dark:focus:bg-gray-900 transition duration-150">
                        </div>

                        <!-- Status ED Filter with Custom Chevron -->
                        <div class="relative w-full sm:w-52">
                            <select name="status_ed" onchange="this.form.submit()"
                                class="h-10 w-full appearance-none rounded-lg border border-gray-200 bg-gray-50/50 pl-3.5 pr-9 text-sm text-gray-700 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900/60 dark:text-gray-200 dark:focus:border-brand-500 dark:focus:bg-gray-900 transition duration-150 cursor-pointer">
                                <option value="" class="dark:bg-gray-900">Semua Status ED</option>
                                <option value="safe" {{ request('status_ed') === 'safe' ? 'selected' : '' }} class="dark:bg-gray-900">✅ Aman (> 30 hari)</option>
                                <option value="expiring" {{ request('status_ed') === 'expiring' ? 'selected' : '' }} class="dark:bg-gray-900">⏰ Mendekati ED (≤ 30 hari)</option>
                                <option value="expired" {{ request('status_ed') === 'expired' ? 'selected' : '' }} class="dark:bg-gray-900">❌ Sudah Kadaluwarsa</option>
                            </select>
                            <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </span>
                        </div>

                        <!-- Tanggal Dari -->
                        <div class="w-full sm:w-36">
                            <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari', request('start_date')) }}"
                                class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50/50 px-3 text-xs text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900/60 dark:text-white/90 dark:focus:border-brand-500 dark:focus:bg-gray-900 transition duration-150"
                                title="Tanggal Masuk Dari">
                        </div>

                        <!-- Tanggal Sampai -->
                        <div class="w-full sm:w-36">
                            <input type="date" name="tanggal_sampai"
                                value="{{ request('tanggal_sampai', request('end_date')) }}"
                                class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50/50 px-3 text-xs text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900/60 dark:text-white/90 dark:focus:border-brand-800 dark:focus:bg-gray-900 transition duration-150"
                                title="Tanggal Masuk Sampai">
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit"
                            class="h-10 inline-flex items-center justify-center gap-1.5 rounded-lg bg-brand-500 px-4 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-500/30 transition duration-150">
                            Filter
                        </button>
                        @if(request()->hasAny(['search', 'status_ed', 'tanggal_dari', 'tanggal_sampai', 'start_date', 'end_date']))
                            <a href="{{ route('obat-masuk.index') }}"
                                class="h-10 inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-3.5 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white transition duration-150"
                                title="Reset Filter">
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
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Supplier</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">No. Batch</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Tgl. ED</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Stok Masuk</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400 text-center">Di Gudang</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400 text-center">Di Rak</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($batches as $trx)
                            <tr
                                class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/20">
                                <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $loop->iteration + ($batches->currentPage() - 1) * $batches->perPage() }}
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-800 dark:text-white/90">
                                    {{ \Carbon\Carbon::parse($trx->tanggal_masuk)->format('d M Y') }}
                                </td>
                                <td class="px-5 py-4 text-sm font-medium text-gray-800 dark:text-white/90">
                                    {{ $trx->obat->nama_obat ?? '-' }}
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $trx->supplier->nama_supplier ?? '-' }}
                                </td>
                                <td class="px-5 py-4 text-sm font-mono text-gray-700 dark:text-gray-300">
                                    {{ $trx->nomor_batch }}
                                </td>
                                <td class="px-5 py-4 text-sm">
                                    @php
                                        $ed = \Carbon\Carbon::parse($trx->tanggal_kadaluwarsa);
                                        $isExpired = $ed->isPast();
                                        $isNear = !$isExpired && $ed->lte(now()->addDays(30));
                                    @endphp
                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $isExpired ? 'bg-error-100 text-error-700 dark:bg-error-900/30 dark:text-error-400' : ($isNear ? 'bg-warning-100 text-warning-700 dark:bg-warning-900/30 dark:text-warning-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300') }}">
                                        {{ $ed->format('d M Y') }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-sm font-bold text-success-600 dark:text-success-400">
                                    +{{ $trx->stok_awal }} {{ $trx->obat->satuan ?? '' }}
                                </td>
                                <td class="px-5 py-4 text-center text-sm font-semibold text-blue-600 dark:text-blue-400">
                                    {{ $trx->stok_gudang }} {{ $trx->obat->satuan ?? '' }}
                                </td>
                                <td class="px-5 py-4 text-center text-sm font-semibold text-green-600 dark:text-green-400">
                                    {{ $trx->stok_rak }} {{ $trx->obat->satuan ?? '' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                    @if(request()->hasAny(['search', 'status_ed', 'tanggal_dari', 'tanggal_sampai']))
                                        Tidak ada transaksi obat masuk yang sesuai dengan filter.
                                    @else
                                        Belum ada transaksi obat masuk.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($batches->hasPages())
                <div class="border-t border-gray-100 p-4 dark:border-gray-800">
                    {{ $batches->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection