@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white/90">Laporan Stok Obat</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Ringkasan stok seluruh obat: Gudang (FEFO) + Display Rak vs ROP.</p>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-gray-dark">
            <div class="border-b border-gray-100 p-4 sm:p-5 dark:border-gray-800">
                <form action="{{ route('laporan.stok-obat') }}" method="GET" class="flex flex-col gap-3.5">
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                        <div class="relative sm:col-span-2">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari nama obat, kode, kategori..."
                                class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50/50 pl-10 pr-4 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900/60 dark:text-white/90 dark:placeholder:text-gray-500 transition">
                        </div>
                        <div class="relative">
                            <select name="status_rop" onchange="this.form.submit()"
                                class="h-10 w-full appearance-none rounded-lg border border-gray-200 bg-gray-50/50 pl-3.5 pr-9 text-sm text-gray-700 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900/60 dark:text-gray-200 transition cursor-pointer">
                                <option value="">Semua Status ROP</option>
                                <option value="kritis" {{ request('status_rop') === 'kritis' ? 'selected' : '' }}>⚠️ Dibawah ROP</option>
                            </select>
                            <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-1 border-t border-gray-100 dark:border-gray-800">
                        <button type="submit" class="h-10 inline-flex items-center justify-center gap-1.5 rounded-lg bg-brand-500 px-5 text-sm font-medium text-white hover:bg-brand-600 transition">Filter</button>
                        @if(request()->hasAny(['search', 'status_rop']))
                            <a href="{{ route('laporan.stok-obat') }}" class="h-10 inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-4 text-sm font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 transition">Reset</a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="max-w-full overflow-x-auto custom-scrollbar">
                <table class="w-full min-w-[900px] text-left">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/20">
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">#</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Nama Obat</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Kategori</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400 text-center">Stok Gudang</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400 text-center">Stok Rak</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400 text-center">Total Apotek</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400 text-center">ROP Min</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400 text-center">Status ROP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $obat)
                            @php
                                $stokGudang = (int) $obat->stok_gudang_total;
                                $stokRak    = (int) $obat->stok_rak_total;
                                $totalApot  = $stokGudang + $stokRak;
                                $isBawahRop = $obat->rop_minimum > 0 && $totalApot <= $obat->rop_minimum;
                            @endphp
                            <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/20 {{ $isBawahRop ? 'bg-error-50/20' : '' }}">
                                <td class="px-5 py-4 text-sm text-gray-500">{{ $loop->iteration + ($data->currentPage() - 1) * $data->perPage() }}</td>
                                <td class="px-5 py-4">
                                    <div class="font-semibold text-sm text-gray-800 dark:text-white/90">{{ $obat->nama_obat }}</div>
                                    @if($obat->kode_obat)<div class="text-xs font-mono text-gray-400">{{ $obat->kode_obat }}</div>@endif
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $obat->kategori ?? '-' }}</td>
                                <td class="px-5 py-4 text-center text-sm font-semibold text-blue-600 dark:text-blue-400">{{ $stokGudang }} <span class="text-xs font-normal text-gray-400">{{ $obat->satuan }}</span></td>
                                <td class="px-5 py-4 text-center text-sm font-semibold text-green-600 dark:text-green-400">{{ $stokRak }} <span class="text-xs font-normal text-gray-400">{{ $obat->satuan }}</span></td>
                                <td class="px-5 py-4 text-center text-sm font-bold text-gray-800 dark:text-white/90">{{ $totalApot }} <span class="text-xs font-normal text-gray-400">{{ $obat->satuan }}</span></td>
                                <td class="px-5 py-4 text-center text-sm text-gray-500 dark:text-gray-400">{{ $obat->rop_minimum }}</td>
                                <td class="px-5 py-4 text-center">
                                    @if($isBawahRop)
                                        <span class="inline-flex items-center rounded-full bg-error-100 px-2.5 py-0.5 text-xs font-semibold text-error-700 dark:bg-error-900/30 dark:text-error-400">⚠️ Kritis</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-success-100 px-2.5 py-0.5 text-xs font-semibold text-success-700 dark:bg-success-900/30 dark:text-success-400">✅ Aman</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Tidak ada data obat.</td></tr>
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
