@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white/90">Laporan Obat Keluar</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Pilih rentang tanggal untuk melihat atau mengunduh
                    laporan.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('laporan.obat-keluar.pdf', ['start_date' => request('tanggal_dari', $startDate), 'end_date' => request('tanggal_sampai', $endDate), 'search' => request('search')]) }}"
                    target="_blank"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-error-500 px-4 py-2 text-sm font-medium text-white shadow-theme-xs hover:bg-error-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Cetak PDF
                </a>
            </div>
        </div>

        <!-- Table Card with Integrated Toolbar -->
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-gray-dark">
            <!-- Toolbar / Filter Header -->
            <div class="border-b border-gray-100 p-4 sm:p-5 dark:border-gray-800">
                <form action="{{ route('laporan.obat-keluar') }}" method="GET" class="flex flex-col gap-3.5">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label for="tanggal_dari"
                                class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Tanggal Mulai</label>
                            <input type="date" id="tanggal_dari" name="tanggal_dari"
                                value="{{ request('tanggal_dari', $startDate) }}" required
                                class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50/50 px-3.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900/60 dark:text-white/90 dark:focus:border-brand-500 dark:focus:bg-gray-900 transition duration-150">
                        </div>
                        <div>
                            <label for="tanggal_sampai"
                                class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Tanggal Selesai</label>
                            <input type="date" id="tanggal_sampai" name="tanggal_sampai"
                                value="{{ request('tanggal_sampai', $endDate) }}" required
                                class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50/50 px-3.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900/60 dark:text-white/90 dark:focus:border-brand-500 dark:focus:bg-gray-900 transition duration-150">
                        </div>
                        <div>
                            <label for="search" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Cari Obat / Batch / Petugas</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400 dark:text-gray-500">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </span>
                                <input type="text" id="search" name="search" value="{{ request('search') }}"
                                    placeholder="Cari nama atau nomor batch..."
                                    class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50/50 pl-10 pr-4 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900/60 dark:text-white/90 dark:placeholder:text-gray-500 dark:focus:border-brand-500 dark:focus:bg-gray-900 transition duration-150">
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 pt-1 border-t border-gray-100 dark:border-gray-800">
                        <button type="submit"
                            class="h-10 inline-flex items-center justify-center gap-1.5 rounded-lg bg-brand-500 px-5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-500/30 transition duration-150">
                            Filter Laporan
                        </button>
                        @if(request()->filled('search'))
                            <a href="{{ route('laporan.obat-keluar', ['tanggal_dari' => request('tanggal_dari', $startDate), 'tanggal_sampai' => request('tanggal_sampai', $endDate)]) }}"
                                class="h-10 inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-4 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white transition duration-150">
                                Reset Pencarian
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div class="max-w-full overflow-x-auto custom-scrollbar">
                <table class="w-full min-w-[800px] text-left">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/20">
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">#</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Obat</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Petugas</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Jumlah Keluar</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Batch FEFO</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $row)
                            <tr
                                class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/20">
                                <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $loop->iteration + ($data->currentPage() - 1) * $data->perPage() }}
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-800 dark:text-white/90">
                                    {{ \Carbon\Carbon::parse($row->tanggal_keluar)->format('d M Y') }}
                                </td>
                                <td class="px-5 py-4 text-sm font-medium text-gray-800 dark:text-white/90">
                                    {{ $row->obat->nama_obat ?? '-' }}
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $row->user->nama_user ?? '-' }}
                                </td>
                                <td class="px-5 py-4 text-sm font-bold text-error-600 dark:text-error-400">
                                    -{{ $row->jumlah }} {{ $row->obat->satuan ?? '' }}
                                </td>
                                <td class="px-5 py-4 text-sm font-mono text-gray-600 dark:text-gray-400">
                                    {{ $row->obatBatch->nomor_batch ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                    Tidak ada data pada rentang tanggal atau filter ini.
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