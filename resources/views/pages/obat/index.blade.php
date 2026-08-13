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
            <a href="{{ route('obat.create') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Obat
            </a>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
    <div class="rounded-lg bg-success-50 p-4 text-success-800 border border-success-200 dark:bg-success-900/20 dark:text-success-400 dark:border-success-800/30">
        {{ session('success') }}
    </div>
    @endif

    <!-- Table -->
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="max-w-full overflow-x-auto custom-scrollbar">
            <table class="w-full min-w-[800px] text-left">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/20">
                        <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Kode Obat</th>
                        <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Nama Obat</th>
                        <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Kategori</th>
                        <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Harga</th>
                        <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Stok Total</th>
                        <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">ROP</th>
                        <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($obats as $obat)
                        <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/20">
                            <td class="px-5 py-4 text-sm text-gray-800 dark:text-white/90">
                                {{ $obat->kode_obat }}
                            </td>
                            <td class="px-5 py-4 text-sm font-medium text-gray-800 dark:text-white/90">
                                {{ $obat->nama_obat }}
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $obat->kategori }}
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">
                                Rp {{ number_format($obat->harga, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4 text-sm">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $obat->stok_total <= $obat->rop_minimum ? 'bg-error-100 text-error-700 dark:bg-error-900/30 dark:text-error-400' : 'bg-success-100 text-success-700 dark:bg-success-900/30 dark:text-success-400' }}">
                                    {{ $obat->stok_total }} {{ $obat->satuan }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $obat->rop_minimum }} {{ $obat->satuan }}
                            </td>
                            <td class="px-5 py-4 text-sm">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('obat.edit', $obat->id) }}" class="text-brand-500 hover:text-brand-600 transition">Edit</a>
                                    <form action="{{ route('obat.destroy', $obat->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-error-500 hover:text-error-600 transition">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                Belum ada data obat.
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
