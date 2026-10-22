@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white/90">Laporan Obat Kadaluwarsa</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Pemantauan batch obat kadaluwarsa dan mendekati kadaluwarsa pada display rak dan gudang.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('laporan.kadaluwarsa.pdf', ['lokasi' => request('lokasi'), 'status_ed' => request('status_ed'), 'search' => request('search')]) }}"
                    target="_blank"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600 transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Cetak PDF
                </a>
            </div>
        </div>

        <!-- Summary Cards (Mengikuti tema Display Rak & Stok Gudang) -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-xl border border-error-200 bg-error-50 p-5 shadow-theme-xs dark:border-error-800/30 dark:bg-error-900/10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-error-600 dark:text-error-400">Expired di Display Rak</p>
                        <p class="mt-1 text-2xl font-bold text-error-700 dark:text-error-300">{{ $expiredDiRak }}</p>
                        <p class="text-xs text-error-600 dark:text-error-400 mt-1">Perlu ditarik dari kasir</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-error-100 dark:bg-error-900/30">
                        <i class="ti ti-alert-triangle text-2xl text-error-500"></i>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-warning-200 bg-warning-50 p-5 shadow-theme-xs dark:border-warning-800/30 dark:bg-warning-900/10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-warning-600 dark:text-warning-400">Mendekati Kadaluwarsa</p>
                        <p class="mt-1 text-2xl font-bold text-warning-700 dark:text-warning-300">{{ $expiring180 }}</p>
                        <p class="text-xs text-warning-600 dark:text-warning-400 mt-1">Sisa waktu &le; 6 bulan</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-warning-100 dark:bg-warning-900/30">
                        <i class="ti ti-clock text-2xl text-warning-500"></i>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-gray-dark">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Expired di Gudang</p>
                        <p class="mt-1 text-2xl font-bold text-gray-800 dark:text-white/90">{{ $expiredDiGudang }}</p>
                        <p class="text-xs text-gray-400 mt-1">Siap proses disposal</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
                        <i class="ti ti-building-warehouse text-2xl text-gray-500 dark:text-gray-400"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Card with Integrated Toolbar -->
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-gray-dark">
            <!-- Toolbar -->
            <div class="border-b border-gray-100 p-4 sm:p-5 dark:border-gray-800">
                <form action="{{ route('laporan.kadaluwarsa') }}" method="GET" class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center">
                        <!-- Search -->
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

                        <!-- Status Filter -->
                        <div class="relative w-full sm:w-56">
                            <select name="status_ed" onchange="this.form.submit()"
                                class="h-10 w-full appearance-none rounded-lg border border-gray-200 bg-gray-50/50 pl-3.5 pr-9 text-sm text-gray-700 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900/60 dark:text-gray-200 transition duration-150 cursor-pointer">
                                <option value="">Semua Status ED</option>
                                <option value="expired" {{ request('status_ed') === 'expired' ? 'selected' : '' }}>Sudah Kadaluwarsa</option>
                                <option value="expiring_30" {{ request('status_ed') === 'expiring_30' ? 'selected' : '' }}>Mendekati ED (&le; 30 Hari)</option>
                                <option value="expiring_90" {{ request('status_ed') === 'expiring_90' ? 'selected' : '' }}>Mendekati ED (&le; 90 Hari)</option>
                                <option value="expiring_180" {{ request('status_ed') === 'expiring_180' ? 'selected' : '' }}>Mendekati ED (&le; 180 Hari)</option>
                            </select>
                            <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </span>
                        </div>

                        <!-- Lokasi Filter -->
                        <div class="relative w-full sm:w-52">
                            <select name="lokasi" onchange="this.form.submit()"
                                class="h-10 w-full appearance-none rounded-lg border border-gray-200 bg-gray-50/50 pl-3.5 pr-9 text-sm text-gray-700 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900/60 dark:text-gray-200 transition duration-150 cursor-pointer">
                                <option value="">Semua Lokasi</option>
                                <option value="rak" {{ request('lokasi') === 'rak' ? 'selected' : '' }}>Ada di Display Rak</option>
                                <option value="gudang" {{ request('lokasi') === 'gudang' ? 'selected' : '' }}>Ada di Gudang</option>
                            </select>
                            <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit"
                            class="h-10 inline-flex items-center justify-center gap-1.5 rounded-lg bg-brand-500 px-4 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-500/30 transition duration-150">
                            Filter
                        </button>
                        @if(request()->hasAny(['search', 'lokasi', 'status_ed']))
                            <a href="{{ route('laporan.kadaluwarsa') }}"
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
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Obat</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">No. Batch</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Supplier</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">
                                <span class="flex items-center gap-1">
                                    <i class="ti ti-sort-ascending text-brand-400"></i> Tgl. ED (FEFO)
                                </span>
                            </th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400 text-center">Stok Rak</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400 text-center">Stok Gudang</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400 text-center">Status</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $batch)
                            @php
                                $ed         = $batch->tanggal_kadaluwarsa;
                                $now        = now()->startOfDay();
                                $isExpired  = $ed->startOfDay()->lt($now);
                                $daysDiff   = (int) $now->diffInDays($ed->startOfDay(), false);
                                $stokRak    = (int) $batch->stok_rak;
                                $stokGudang = (int) $batch->stok_gudang;
                                $expiredDiRakAktif = $isExpired && $stokRak > 0;
                            @endphp
                            <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/20 {{ $expiredDiRakAktif ? 'bg-error-50/30 dark:bg-error-900/5' : '' }}">
                                <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $loop->iteration + ($data->currentPage() - 1) * $data->perPage() }}
                                </td>
                                <td class="px-5 py-4">
                                    <div class="font-semibold text-sm text-gray-800 dark:text-white/90">{{ $batch->obat->nama_obat ?? '-' }}</div>
                                    @if($batch->obat->kode_obat)
                                        <div class="text-xs text-gray-400 font-mono">{{ $batch->obat->kode_obat }}</div>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-sm font-mono text-gray-700 dark:text-gray-300">
                                    {{ $batch->nomor_batch ?? '-' }}
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $batch->supplier->nama_supplier ?? '-' }}
                                </td>
                                <td class="px-5 py-4 text-sm">
                                    <div class="font-semibold {{ $isExpired ? 'text-error-600 dark:text-error-400' : 'text-gray-800 dark:text-white/90' }}">
                                        {{ $ed->format('d M Y') }}
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        @if($isExpired)
                                            Lewat {{ abs($daysDiff) }} hari
                                        @elseif($daysDiff === 0)
                                            Hari ini
                                        @else
                                            Sisa {{ $daysDiff }} hari
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="text-sm font-bold {{ $expiredDiRakAktif ? 'text-error-600 dark:text-error-400' : 'text-gray-700 dark:text-gray-300' }}">
                                        {{ $stokRak }}
                                    </span>
                                    <span class="text-xs text-gray-400 ml-0.5">{{ $batch->obat->satuan_jual ?? '' }}</span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="text-sm font-bold text-gray-700 dark:text-gray-300">
                                        {{ $stokGudang }}
                                    </span>
                                    <span class="text-xs text-gray-400 ml-0.5">{{ $batch->obat->satuan_beli ?? '' }}</span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    @if($isExpired)
                                        <span class="inline-flex items-center rounded-full bg-error-100 px-2.5 py-0.5 text-xs font-semibold text-error-700 dark:bg-error-900/30 dark:text-error-400">
                                            Expired
                                        </span>
                                    @elseif($daysDiff <= 30)
                                        <span class="inline-flex items-center rounded-full bg-warning-100 px-2.5 py-0.5 text-xs font-semibold text-warning-700 dark:bg-warning-900/30 dark:text-warning-400">
                                            &le; 30 Hari
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-warning-50 px-2.5 py-0.5 text-xs font-semibold text-warning-600 dark:bg-warning-900/20 dark:text-warning-400">
                                            Mendekati ED
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-center">
                                    @if($isExpired && ($stokRak > 0 || $stokGudang > 0))
                                        <a href="{{ route('obat-keluar.create', ['batch_id' => $batch->id]) }}"
                                            class="inline-flex items-center gap-1 rounded-lg border border-red-200 bg-red-50 px-2.5 py-1 text-xs font-medium text-red-700 hover:bg-red-100 dark:border-red-800/40 dark:bg-red-900/20 dark:text-red-400 transition"
                                            title="Tarik ke formulir pembuangan obat">
                                            <i class="ti ti-trash text-sm"></i> Tarik Obat
                                        </a>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                    Tidak ada data obat kadaluwarsa atau mendekati ED yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($data->hasPages())
                <div class="border-t border-gray-100 p-4 dark:border-gray-800">
                    {{ $data->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
