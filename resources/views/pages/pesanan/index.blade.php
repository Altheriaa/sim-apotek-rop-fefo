@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white/90">Data Pesanan (ROP)</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Daftar pesanan otomatis yang digenerate oleh sistem ROP (Reorder Point).</p>
        </div>
        <div class="flex gap-2">
            <!-- Bisa tambahkan filter atau tombol manual pesan -->
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
            <table class="w-full min-w-[1000px] text-left">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/20">
                        <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Pesan</th>
                        <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Obat</th>
                        <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Supplier</th>
                        <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Jumlah Pesan</th>
                        <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Total Harga</th>
                        <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Status</th>
                        <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pesanans as $pesanan)
                        <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/20">
                            <td class="px-5 py-4 text-sm text-gray-800 dark:text-white/90">
                                {{ \Carbon\Carbon::parse($pesanan->tanggal_pesan)->format('d M Y') }}
                            </td>
                            <td class="px-5 py-4 text-sm font-medium text-gray-800 dark:text-white/90">
                                {{ $pesanan->obat->nama_obat }}
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $pesanan->supplier->nama_supplier ?? '-' }}
                            </td>
                            <td class="px-5 py-4 text-sm font-bold text-gray-800 dark:text-white/90">
                                {{ $pesanan->jumlah_pesan }}
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">
                                Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4 text-sm">
                                @php
                                    $statusClass = [
                                        'draft' => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
                                        'diproses' => 'bg-warning-100 text-warning-700 dark:bg-warning-900/30 dark:text-warning-400',
                                        'dikirim' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                        'selesai' => 'bg-success-100 text-success-700 dark:bg-success-900/30 dark:text-success-400',
                                        'batal' => 'bg-error-100 text-error-700 dark:bg-error-900/30 dark:text-error-400',
                                    ];
                                    $class = $statusClass[$pesanan->status] ?? 'bg-gray-100 text-gray-700';
                                @endphp
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $class }}">
                                    {{ ucfirst($pesanan->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-sm">
                                <a href="{{ route('pesanan.show', $pesanan->id) }}" class="text-brand-500 hover:text-brand-600 transition font-medium">Detail / Update</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                Belum ada data pesanan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pesanans->hasPages())
        <div class="border-t border-gray-100 p-4 dark:border-gray-800">
            {{ $pesanans->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
