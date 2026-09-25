@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white/90">Pembuangan / Disposal Obat</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Riwayat pencatatan batch obat yang dibuang karena expired atau rusak.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('obat-keluar.create') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-red-500 px-4 py-2 text-sm font-medium text-white shadow-theme-xs hover:bg-red-600">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Catat Pembuangan
            </a>
        </div>
    </div>

    <!-- Flash -->
    @if(session('success'))
        <x-common.flash-alert type="success" :message="session('success')" />
    @endif
    @if(session('error'))
        <x-common.flash-alert type="error" :message="session('error')" />
    @endif

    <!-- Filter Toolbar -->
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-gray-dark">
        <div class="border-b border-gray-100 p-4 sm:p-5 dark:border-gray-800">
            <form action="{{ route('obat-keluar.index') }}" method="GET" class="flex flex-col gap-3.5 sm:flex-row sm:items-center sm:justify-between">
                <div class="grid flex-1 grid-cols-1 gap-3 sm:grid-cols-4">
                    <!-- Search -->
                    <div class="relative sm:col-span-2">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama obat atau no. batch..."
                               class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50/50 pl-10 pr-4 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900/60 dark:text-white/90 dark:placeholder:text-gray-500 transition duration-150">
                    </div>
                    <!-- Filter Alasan -->
                    <div>
                        <select name="alasan" class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50/50 px-3.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900/60 dark:text-white/90 transition duration-150">
                            <option value="">Semua Alasan</option>
                            @foreach($alasanOptions as $key => $label)
                                <option value="{{ $key }}" {{ request('alasan') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Filter Tanggal -->
                    <div>
                        <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}"
                               class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50/50 px-3.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900/60 dark:text-white/90 transition duration-150">
                    </div>
                </div>
                <div class="flex shrink-0 gap-2">
                    <button type="submit" class="h-10 rounded-lg bg-brand-500 px-5 text-sm font-medium text-white hover:bg-brand-600 transition">Filter</button>
                    <a href="{{ route('obat-keluar.index') }}" class="h-10 rounded-lg border border-gray-200 bg-white px-4 text-sm font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 inline-flex items-center transition">Reset</a>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/70 dark:border-gray-800 dark:bg-gray-900/40">
                        <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">#</th>
                        <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal</th>
                        <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Nama Obat</th>
                        <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">No. Batch</th>
                        <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">ED Batch</th>
                        <th class="px-5 py-3 text-center text-sm font-medium text-gray-500 dark:text-gray-400">Dibuang (Gudang)</th>
                        <th class="px-5 py-3 text-center text-sm font-medium text-gray-500 dark:text-gray-400">Dibuang (Rak)</th>
                        <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Alasan</th>
                        <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Petugas</th>
                        <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($disposals as $d)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                            <td class="px-5 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $disposals->firstItem() + $loop->index }}</td>
                            <td class="px-5 py-3 text-sm text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                {{ $d->tanggal_keluar->format('d/m/Y') }}
                            </td>
                            <td class="px-5 py-3">
                                <span class="font-medium text-sm text-gray-900 dark:text-white">{{ $d->obat->nama_obat ?? '-' }}</span>
                            </td>
                            <td class="px-5 py-3 text-sm font-mono text-gray-600 dark:text-gray-400">
                                {{ $d->obatBatch->nomor_batch ?? '-' }}
                            </td>
                            <td class="px-5 py-3 text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                @if($d->obatBatch)
                                    @if($d->obatBatch->tanggal_kadaluwarsa->lt(now()))
                                        <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                            <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                            {{ $d->obatBatch->tanggal_kadaluwarsa->format('d/m/Y') }} (Expired)
                                        </span>
                                    @else
                                        {{ $d->obatBatch->tanggal_kadaluwarsa->format('d/m/Y') }}
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-5 py-3 text-center text-sm text-gray-700 dark:text-gray-300">
                                @if($d->jumlah_gudang > 0)
                                    <span class="font-semibold">{{ $d->jumlah_gudang }}</span>
                                    <span class="text-xs text-gray-400">{{ $d->obat->satuan_beli ?? '' }}</span>
                                @else
                                    <span class="text-gray-300 dark:text-gray-600">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-center text-sm text-gray-700 dark:text-gray-300">
                                @if($d->jumlah_rak > 0)
                                    <span class="font-semibold">{{ $d->jumlah_rak }}</span>
                                    <span class="text-xs text-gray-400">{{ $d->obat->satuan_jual ?? '' }}</span>
                                @else
                                    <span class="text-gray-300 dark:text-gray-600">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-sm">
                                @php
                                    $badgeClass = match($d->alasan) {
                                        'expired' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                        'rusak'   => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
                                        default   => 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400',
                                    };
                                @endphp
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $badgeClass }}">
                                    {{ $d->alasan_label }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-sm text-gray-600 dark:text-gray-400">
                                {{ $d->user->nama_user ?? $d->user->name ?? '-' }}
                            </td>
                            <td class="px-5 py-3 text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate">
                                {{ $d->catatan ?? '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-5 py-12 text-center">
                                <div class="flex flex-col items-center gap-2 text-gray-400 dark:text-gray-600">
                                    <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    <p class="text-sm font-medium">Belum ada riwayat pembuangan obat.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($disposals->hasPages())
            <div class="border-t border-gray-100 p-4 dark:border-gray-800">
                {{ $disposals->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
