@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white/90">Data Supplier</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Kelola master data pemasok obat.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('supplier.create') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Supplier
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
                        <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Nama Supplier</th>
                        <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Kontak (WhatsApp)</th>
                        <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Alamat</th>
                        <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                        <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/20">
                            <td class="px-5 py-4 text-sm font-medium text-gray-800 dark:text-white/90">
                                {{ $supplier->nama_supplier }}
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $supplier->kontak }}
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $supplier->alamat }}
                            </td>
                            <td class="px-5 py-4 text-sm">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('supplier.edit', $supplier->id) }}" class="text-brand-500 hover:text-brand-600 transition">Edit</a>
                                    <form action="{{ route('supplier.destroy', $supplier->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-error-500 hover:text-error-600 transition">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                Belum ada data supplier.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($suppliers->hasPages())
        <div class="border-t border-gray-100 p-4 dark:border-gray-800">
            {{ $suppliers->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
