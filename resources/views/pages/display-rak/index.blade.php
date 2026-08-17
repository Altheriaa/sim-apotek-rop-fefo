@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white/90">Display Rak Obat</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Monitor stok obat yang tersedia di display rak penjualan.</p>
            </div>
            @if(auth()->user()->isAdmin())
                <a href="{{ route('transfer-rak.create') }}"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">
                    <i class="ti ti-circle-arrow-right text-base"></i>
                    Transfer dari Gudang
                </a>
            @endif
        </div>

        {{-- Alerts --}}
        @if(session('success'))
            <div class="rounded-lg bg-success-50 p-4 text-success-800 border border-success-200 dark:bg-success-900/20 dark:text-success-400 dark:border-success-800/30">
                {{ session('success') }}
            </div>
        @endif

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-gray-dark">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Jenis Obat</p>
                        <p class="mt-1 text-2xl font-bold text-gray-800 dark:text-white/90">{{ $totalObat }}</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-brand-50 dark:bg-brand-900/20">
                        <i class="ti ti-pill text-2xl text-brand-500"></i>
                    </div>
                </div>
            </div>
            <div class="rounded-xl border border-warning-200 bg-warning-50 p-5 shadow-theme-xs dark:border-warning-800/30 dark:bg-warning-900/10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-warning-600 dark:text-warning-400">Rak Kritis (Perlu Transfer)</p>
                        <p class="mt-1 text-2xl font-bold text-warning-700 dark:text-warning-300">{{ $obatKritis }}</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-warning-100 dark:bg-warning-900/30">
                        <i class="ti ti-alert-triangle text-2xl text-warning-500"></i>
                    </div>
                </div>
            </div>
            <div class="rounded-xl border border-error-200 bg-error-50 p-5 shadow-theme-xs dark:border-error-800/30 dark:bg-error-900/10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-error-600 dark:text-error-400">Rak Kosong</p>
                        <p class="mt-1 text-2xl font-bold text-error-700 dark:text-error-300">{{ $obatHabisRak }}</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-error-100 dark:bg-error-900/30">
                        <i class="ti ti-x text-2xl text-error-500"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table Card --}}
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-gray-dark">
            {{-- Toolbar --}}
            <div class="border-b border-gray-100 p-4 sm:p-5 dark:border-gray-800">
                <form action="{{ route('display-rak.index') }}" method="GET"
                    class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center">
                        {{-- Search --}}
                        <div class="relative flex-1 max-w-md">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari nama obat, kode, kategori..."
                                class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50/50 pl-10 pr-4 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900/60 dark:text-white/90 dark:placeholder:text-gray-500 transition duration-150">
                        </div>
                        {{-- Status Rak Filter --}}
                        <div class="relative w-full sm:w-56">
                            <select name="status_rak" onchange="this.form.submit()"
                                class="h-10 w-full appearance-none rounded-lg border border-gray-200 bg-gray-50/50 pl-3.5 pr-9 text-sm text-gray-700 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900/60 dark:text-gray-200 transition duration-150 cursor-pointer">
                                <option value="">Semua Status Rak</option>
                                <option value="kritis" {{ request('status_rak') === 'kritis' ? 'selected' : '' }}>Kritis (≤ Min Stok)</option>
                                <option value="aman" {{ request('status_rak') === 'aman' ? 'selected' : '' }}>Aman</option>
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
                        @if(request()->hasAny(['search', 'status_rak']))
                            <a href="{{ route('display-rak.index') }}"
                                class="h-10 inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-3.5 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white transition duration-150"
                                title="Reset Filter">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Table --}}
            <div class="max-w-full overflow-x-auto custom-scrollbar">
                <table class="w-full min-w-[700px] text-left">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/20">
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">#</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Nama Obat</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Kategori</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400 text-center">Stok Rak</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400 text-center">Min. Rak</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400 text-center">Stok Gudang</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400 text-center">Status</th>
                            @if(auth()->user()->isAdmin())
                                <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400 text-center">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($obats as $obat)
                            @php
                                $stokRak    = (int) $obat->stok_rak_total;
                                $stokGudang = (int) $obat->stok_gudang_total;
                                $isKosong   = $stokRak === 0;
                                $isKritis   = !$isKosong && $stokRak <= $obat->min_stok_rak;
                            @endphp
                            <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/20 {{ $isKosong ? 'bg-error-50/30 dark:bg-error-900/5' : ($isKritis ? 'bg-warning-50/30 dark:bg-warning-900/5' : '') }}">
                                <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $loop->iteration + ($obats->currentPage() - 1) * $obats->perPage() }}
                                </td>
                                <td class="px-5 py-4">
                                    <div class="font-semibold text-sm text-gray-800 dark:text-white/90">{{ $obat->nama_obat }}</div>
                                    @if($obat->kode_obat)
                                        <div class="text-xs text-gray-400 font-mono">{{ $obat->kode_obat }}</div>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $obat->kategori ?? '-' }}
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="text-lg font-bold {{ $isKosong ? 'text-error-600 dark:text-error-400' : ($isKritis ? 'text-warning-600 dark:text-warning-400' : 'text-success-600 dark:text-success-400') }}">
                                        {{ $stokRak }}
                                    </span>
                                    <span class="text-xs text-gray-400 ml-0.5">{{ $obat->satuan }}</span>
                                </td>
                                <td class="px-5 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                    {{ $obat->min_stok_rak }} {{ $obat->satuan }}
                                </td>
                                <td class="px-5 py-4 text-center text-sm text-gray-600 dark:text-gray-300">
                                    {{ $stokGudang }} {{ $obat->satuan }}
                                </td>
                                <td class="px-5 py-4 text-center">
                                    @if($isKosong)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-error-100 px-2.5 py-0.5 text-xs font-semibold text-error-700 dark:bg-error-900/30 dark:text-error-400">
                                            Kosong
                                        </span>
                                    @elseif($isKritis)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-warning-100 px-2.5 py-0.5 text-xs font-semibold text-warning-700 dark:bg-warning-900/30 dark:text-warning-400">
                                             Kritis
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-success-100 px-2.5 py-0.5 text-xs font-semibold text-success-700 dark:bg-success-900/30 dark:text-success-400">
                                            Aman
                                        </span>
                                    @endif
                                </td>
                                @if(auth()->user()->isAdmin())
                                <td class="px-5 py-4 text-center">
                                    @if($isKosong || $isKritis)
                                        <a href="{{ route('transfer-rak.create', ['obat_id' => $obat->id]) }}"
                                            class="inline-flex items-center gap-1 rounded-lg bg-warning-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-warning-600 transition">
                                            <i class="ti ti-circle-arrow-right text-sm"></i> Transfer
                                        </a>
                                    @else
                                        <span class="text-xs text-gray-400">—</span>
                                    @endif
                                </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->isAdmin() ? 8 : 7 }}" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                    Tidak ada data obat yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($obats->hasPages())
                <div class="border-t border-gray-100 p-4 dark:border-gray-800">
                    {{ $obats->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
