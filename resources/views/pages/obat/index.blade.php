@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white/90">Data Obat</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Kelola master data obat dan informasi ROP.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('obat.create') }}"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Obat
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
        <div
            class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-gray-dark">
            <!-- Toolbar / Filter Header -->
            <div class="border-b border-gray-100 p-4 sm:p-5 dark:border-gray-800">
                <form action="{{ route('obat.index') }}" method="GET"
                    class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center">
                        <!-- Search Input -->
                        <div class="relative flex-1 max-w-md">
                            <span
                                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400 dark:text-gray-500">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari nama obat atau satuan..."
                                class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50/50 pl-10 pr-4 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900/60 dark:text-white/90 dark:placeholder:text-gray-500 dark:focus:border-brand-500 dark:focus:bg-gray-900 transition duration-150">
                        </div>

                        <!-- Status ROP Filter with Custom Chevron -->
                        <div class="relative w-full sm:w-56">
                            <select name="status" onchange="this.form.submit()"
                                class="h-10 w-full appearance-none rounded-lg border border-gray-200 bg-gray-50/50 pl-3.5 pr-9 text-sm text-gray-700 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900/60 dark:text-gray-200 dark:focus:border-brand-500 dark:focus:bg-gray-900 transition duration-150 cursor-pointer">
                                <option value="" class="dark:bg-gray-900">Semua Status Stok</option>
                                <option value="rop" {{ request('status') === 'rop' ? 'selected' : '' }}
                                    class="dark:bg-gray-900">⚠️ Perlu Reorder (≤ ROP)</option>
                                <option value="aman" {{ request('status') === 'aman' ? 'selected' : '' }}
                                    class="dark:bg-gray-900">✅ Stok Aman (> ROP)</option>
                            </select>
                            <span
                                class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit"
                            class="h-10 inline-flex items-center justify-center gap-1.5 rounded-lg bg-brand-500 px-4 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-500/30 transition duration-150">
                            Cari
                        </button>
                        @if(request()->hasAny(['search', 'status']))
                            <a href="{{ route('obat.index') }}"
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
                <table class="w-full min-w-[700px] text-left">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/20">
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">#</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Kode Obat</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Nama Obat</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Satuan</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Stok Total</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">ROP Minimum</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Status Stok</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Harga</th>
                            <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($obats as $obat)
                            @php
                                $stokGudang = (int) ($obat->total_stok_gudang ?? 0);
                                $stokRak = (int) ($obat->total_stok_rak ?? 0);
                                $stokTotal = $stokGudang + $stokRak;
                                $isRop = $stokTotal <= $obat->rop_minimum;
                            @endphp
                            <tr
                                class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/20">
                                <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $loop->iteration + ($obats->currentPage() - 1) * $obats->perPage() }}
                                </td>
                                <td class="px-5 py-4 text-sm font-medium text-gray-800 dark:text-white/90">
                                    <a href="{{ route('obat.show', $obat->id) }}" class="hover:text-brand-500 transition">
                                        {{ $obat->kode_obat }}
                                    </a>
                                </td>
                                <td class="px-5 py-4 text-sm font-medium text-gray-800 dark:text-white/90">
                                    <a href="{{ route('obat.show', $obat->id) }}" class="hover:text-brand-500 transition">
                                        {{ $obat->nama_obat }}
                                    </a>
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $obat->satuan }}
                                </td>
                                <td class="px-5 py-4 text-sm font-bold text-gray-800 dark:text-white/90">
                                    {{ $stokTotal }} {{ $obat->satuan }}
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $obat->rop_minimum }} {{ $obat->satuan }}
                                </td>
                                <td class="px-5 py-4 text-sm">
                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $isRop ? 'bg-error-100 text-error-700 dark:bg-error-900/30 dark:text-error-400' : 'bg-success-100 text-success-700 dark:bg-success-900/30 dark:text-success-400' }}">
                                        {{ $isRop ? ' Perlu Reorder' : ' Aman' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    Rp {{ $obat->harga }}
                                </td>
                                <td class="px-5 py-4 text-sm">
                                    <div class="flex items-center gap-1.5">
                                        <a href="{{ route('obat.show', $obat->id) }}"
                                            class="p-1.5 rounded-lg text-gray-500 hover:text-brand-500 hover:bg-brand-50 dark:text-gray-400 dark:hover:bg-brand-900/20 dark:hover:text-brand-400 transition"
                                            title="Lihat Detail & Batch">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('obat.edit', $obat->id) }}"
                                            class="p-1.5 rounded-lg text-gray-500 hover:text-brand-500 hover:bg-brand-50 dark:text-gray-400 dark:hover:bg-brand-900/20 dark:hover:text-brand-400 transition"
                                            title="Edit Obat">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('obat.destroy', $obat->id) }}" method="POST" class="inline"
                                            onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-1.5 rounded-lg text-gray-500 hover:text-error-500 hover:bg-error-50 dark:text-gray-400 dark:hover:bg-error-900/20 dark:hover:text-error-400 transition"
                                                title="Hapus Obat">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                    @if(request()->hasAny(['search', 'status']))
                                        Tidak ada data obat yang sesuai dengan filter pencarian.
                                    @else
                                        Belum ada data obat.
                                    @endif
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