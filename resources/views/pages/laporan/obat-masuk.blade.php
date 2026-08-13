@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white/90">Laporan Obat Masuk</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Pilih rentang tanggal untuk melihat atau mengunduh laporan.</p>
        </div>
        <div class="flex gap-2">
            @if(request('tanggal_dari') && request('tanggal_sampai'))
                <a href="{{ route('laporan.obat-masuk.pdf', ['start_date' => request('tanggal_dari'), 'end_date' => request('tanggal_sampai')]) }}" target="_blank" class="inline-flex items-center gap-1.5 rounded-lg bg-error-500 px-4 py-2 text-sm font-medium text-white shadow-theme-xs hover:bg-error-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Cetak PDF
                </a>
            @endif
        </div>
    </div>

    <!-- Filter Form -->
    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-sm dark:border-gray-800 dark:bg-gray-dark">
        <form action="{{ route('laporan.obat-masuk') }}" method="GET" class="flex flex-col sm:flex-row items-end gap-4">
            <div class="w-full sm:w-1/3">
                <label for="tanggal_dari" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Mulai</label>
                <input type="date" id="tanggal_dari" name="tanggal_dari" value="{{ request('tanggal_dari', $startDate) }}" required
                       class="w-full rounded-lg border border-gray-200 bg-transparent px-4 py-2 text-sm text-gray-800 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-800 dark:text-white/90 dark:focus:border-brand-800">
            </div>
            <div class="w-full sm:w-1/3">
                <label for="tanggal_sampai" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Selesai</label>
                <input type="date" id="tanggal_sampai" name="tanggal_sampai" value="{{ request('tanggal_sampai', $endDate) }}" required
                       class="w-full rounded-lg border border-gray-200 bg-transparent px-4 py-2 text-sm text-gray-800 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-800 dark:text-white/90 dark:focus:border-brand-800">
            </div>
            <div class="w-full sm:w-1/3">
                <button type="submit" class="w-full rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">
                    Tampilkan
                </button>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="max-w-full overflow-x-auto custom-scrollbar">
            <table class="w-full min-w-[800px] text-left">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/20">
                        <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal</th>
                        <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Obat</th>
                        <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Supplier</th>
                        <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">No. Batch</th>
                        <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $row)
                        <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/20">
                            <td class="px-5 py-4 text-sm text-gray-800 dark:text-white/90">
                                {{ \Carbon\Carbon::parse($row->tanggal_masuk)->format('d M Y') }}
                            </td>
                            <td class="px-5 py-4 text-sm font-medium text-gray-800 dark:text-white/90">
                                {{ $row->obat->nama_obat }}
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $row->supplier->nama_supplier ?? '-' }}
                            </td>
                            <td class="px-5 py-4 text-sm text-brand-600 dark:text-brand-400 font-medium">
                                {{ $row->nomor_batch ?? '-' }}
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-800 dark:text-white/90">
                                {{ $row->stok_awal }} {{ $row->obat->satuan }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                Tidak ada data pada rentang tanggal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
