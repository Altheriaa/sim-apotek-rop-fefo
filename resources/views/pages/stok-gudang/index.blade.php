@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white/90">Stok Gudang (FEFO)</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Daftar batch obat di gudang, diurutkan berdasarkan tanggal kadaluwarsa terdekat (FEFO).</p>
            </div>
            <a href="{{ route('obat-masuk.create') }}"
                class="inline-flex items-center gap-1.5 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">
                <i class="ti ti-circle-arrow-down text-base"></i>
                Catat Obat Masuk
            </a>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-gray-dark">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Batch Aktif</p>
                <p class="mt-1 text-2xl font-bold text-gray-800 dark:text-white/90">{{ $totalBatch }}</p>
            </div>
            <div class="rounded-xl border border-warning-200 bg-warning-50 p-5 shadow-theme-xs dark:border-warning-800/30 dark:bg-warning-900/10">
                <p class="text-sm text-warning-600 dark:text-warning-400">Mendekati Kadaluwarsa</p>
                <p class="mt-1 text-2xl font-bold text-warning-700 dark:text-warning-300">{{ $expiringBatch }}</p>
            </div>
            <div class="rounded-xl border border-error-200 bg-error-50 p-5 shadow-theme-xs dark:border-error-800/30 dark:bg-error-900/10">
                <p class="text-sm text-error-600 dark:text-error-400">Sudah Kadaluwarsa</p>
                <p class="mt-1 text-2xl font-bold text-error-700 dark:text-error-300">{{ $expiredBatch }}</p>
            </div>
        </div>

        {{-- Table Card --}}
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-gray-dark">
            {{-- Toolbar --}}
            <div class="border-b border-gray-100 p-4 sm:p-5 dark:border-gray-800">
                <form action="{{ route('stok-gudang.index') }}" method="GET"
                    class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center">
                        {{-- Search Input --}}
                        <div class="relative flex-1 max-w-md">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari nama obat, no. batch, supplier..."
                                class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50/50 pl-10 pr-4 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900/60 dark:text-white/90 dark:placeholder:text-gray-500 transition duration-150">
                        </div>

                        {{-- Status ED Filter --}}
                        <div class="relative w-full sm:w-60">
                            <select name="status_ed" onchange="this.form.submit()"
                                class="h-10 w-full appearance-none rounded-lg border border-gray-200 bg-gray-50/50 pl-3.5 pr-9 text-sm text-gray-700 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900/60 dark:text-gray-200 transition duration-150 cursor-pointer">
                                <option value="">Semua Status ED</option>
                                <option value="safe" {{ request('status_ed') === 'safe' ? 'selected' : '' }}>Aman (> 30 hari)</option>
                                <option value="expiring" {{ request('status_ed') === 'expiring' ? 'selected' : '' }}>Mendekati ED (≤ 30 hari)</option>
                                <option value="expired" {{ request('status_ed') === 'expired' ? 'selected' : '' }}>Sudah Kadaluwarsa</option>
                            </select>
                            <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    {{-- Buttons --}}
                    <div class="flex items-center gap-2">
                        <button type="submit"
                            class="h-10 inline-flex items-center justify-center gap-1.5 rounded-lg bg-brand-500 px-4 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-500/30 transition duration-150">
                            Filter
                        </button>
                        @if(request()->hasAny(['search', 'status_ed']))
                            <a href="{{ route('stok-gudang.index') }}"
                                class="h-10 inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-3.5 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white transition duration-150"
                                title="Reset Filter">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="max-w-full overflow-x-auto custom-scrollbar">
                <table class="w-full min-w-[900px] text-left">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/20">
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">#</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Obat</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">No. Batch</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Supplier</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Tgl Masuk</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">
                                <span class="flex items-center gap-1">
                                    <i class="ti ti-sort-ascending text-brand-400"></i> Tgl. ED (FEFO)
                                </span>
                            </th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400 text-center">Stok Gudang</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400 text-center">Stok Rak</th>
                            <th class="px-18 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Status ED</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($batches as $batch)
                            @php
                                $ed        = $batch->tanggal_kadaluwarsa;
                                $isExpired = $ed->isPast();
                                $isNear    = !$isExpired && $ed->lte(now()->addDays(30));
                            @endphp
                            <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/20">
                                <td class="px-5 py-4 text-sm text-gray-500">
                                    {{ $loop->iteration + ($batches->currentPage() - 1) * $batches->perPage() }}
                                </td>
                                <td class="px-5 py-4">
                                    <div class="font-semibold text-sm text-gray-800 dark:text-white/90">{{ $batch->obat->nama_obat ?? '-' }}</div>
                                    <div class="text-xs text-gray-400">{{ $batch->obat->satuan ?? '' }}</div>
                                </td>
                                <td class="px-5 py-4 text-sm font-mono text-gray-700 dark:text-gray-300">{{ $batch->nomor_batch ?? '-' }}</td>
                                <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $batch->supplier->nama_supplier ?? '-' }}</td>
                                <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $batch->tanggal_masuk->format('d M Y') }}</td>
                                <td class="px-5 py-4 text-sm font-semibold {{ $isExpired ? 'text-error-600 dark:text-error-400' : ($isNear ? 'text-warning-600 dark:text-warning-400' : 'text-gray-800 dark:text-white/90') }}">
                                    {{ $ed->format('d M Y') }}
                                </td>
                                <td class="px-5 py-4 text-center font-bold text-blue-600 dark:text-blue-400">{{ $batch->stok_gudang }}</td>
                                <td class="px-5 py-4 text-center font-semibold text-gray-600 dark:text-gray-300">{{ $batch->stok_rak }}</td>
                                <td class="px-5 py-4 text-center">
                                    @if($isExpired)
                                        <span class="inline-flex items-center rounded-full bg-error-100 px-2.5 py-0.5 text-xs font-semibold text-error-700 dark:bg-error-900/30 dark:text-error-400">Expired</span>
                                    @elseif($isNear)
                                        <span class="inline-flex items-center rounded-full bg-warning-100 px-2.5 py-0.5 text-xs font-semibold text-warning-700 dark:bg-warning-900/30 dark:text-warning-400">Near Expired Date</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-success-100 px-2.5 py-0.5 text-xs font-semibold text-success-700 dark:bg-success-900/30 dark:text-success-400">Stock Aman</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                    Tidak ada batch stok gudang yang ditemukan.
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
