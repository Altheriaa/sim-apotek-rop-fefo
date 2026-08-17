@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white/90">Laporan Penjualan</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Rekap seluruh transaksi kasir dalam periode tertentu.</p>
        </div>

        @if($totalPendapatan > 0)
            <div class="rounded-xl border border-brand-200 bg-brand-50/70 p-4 dark:border-brand-800/30 dark:bg-brand-900/10 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-100 dark:bg-brand-900/30">
                        <i class="ti ti-currency-rupiah text-xl text-brand-500"></i>
                    </div>
                    <div>
                        <p class="text-xs text-brand-600 dark:text-brand-400">Total Pendapatan Periode</p>
                        <p class="text-2xl font-bold text-brand-700 dark:text-brand-300">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
                    </div>
                </div>
                <div class="text-right text-xs text-brand-500 dark:text-brand-400">
                    {{ $startDate }} — {{ $endDate }}
                </div>
            </div>
        @endif

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-gray-dark">
            <div class="border-b border-gray-100 p-4 sm:p-5 dark:border-gray-800">
                <form action="{{ route('laporan.penjualan') }}" method="GET" class="flex flex-col gap-3.5">
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                        <div class="relative lg:col-span-2">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari no. transaksi, nama pembeli..."
                                class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50/50 pl-10 pr-4 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900/60 dark:text-white/90 dark:placeholder:text-gray-500 transition">
                        </div>
                        <div>
                            <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari', $startDate) }}"
                                class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50/50 px-3.5 text-sm focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900/60 dark:text-white/90 transition"
                                title="Dari Tanggal">
                        </div>
                        <div>
                            <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai', $endDate) }}"
                                class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50/50 px-3.5 text-sm focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900/60 dark:text-white/90 transition"
                                title="Sampai Tanggal">
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-1 border-t border-gray-100 dark:border-gray-800">
                        <button type="submit" class="h-10 inline-flex items-center justify-center gap-1.5 rounded-lg bg-brand-500 px-5 text-sm font-medium text-white hover:bg-brand-600 transition">Filter</button>
                        @if(request()->hasAny(['search', 'tanggal_dari', 'tanggal_sampai']))
                            <a href="{{ route('laporan.penjualan') }}" class="h-10 inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-4 text-sm font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 transition">Reset</a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="max-w-full overflow-x-auto custom-scrollbar">
                <table class="w-full min-w-[800px] text-left">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/20">
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">#</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">No. Transaksi</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Pembeli</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Item</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400 text-right">Total</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Kasir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $trx)
                            <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/20">
                                <td class="px-5 py-4 text-sm text-gray-500">{{ $loop->iteration + ($data->currentPage() - 1) * $data->perPage() }}</td>
                                <td class="px-5 py-4 text-sm font-mono font-semibold text-brand-600 dark:text-brand-400">{{ $trx->no_transaksi }}</td>
                                <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $trx->tanggal_transaksi->format('d M Y, H:i') }}</td>
                                <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $trx->nama_pembeli ?? 'Umum' }}</td>
                                <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    @foreach($trx->details->take(2) as $d)
                                        <div class="text-xs">{{ $d->obat->nama_obat ?? '-' }} ({{ $d->jumlah }})</div>
                                    @endforeach
                                    @if($trx->details->count() > 2)
                                        <div class="text-xs text-gray-400">+{{ $trx->details->count() - 2 }} lainnya</div>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-sm font-bold text-gray-800 dark:text-white/90 text-right">Rp {{ number_format($trx->total_harga, 0, ',', '.') }}</td>
                                <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $trx->user->nama_user ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Tidak ada transaksi penjualan pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($data->hasPages())
                <div class="border-t border-gray-100 p-4 dark:border-gray-800">{{ $data->links() }}</div>
            @endif
        </div>
    </div>
@endsection
