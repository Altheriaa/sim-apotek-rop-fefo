@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white/90">Detail Pesanan</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ $pesanan->kode_pesanan }} &mdash; {{ \Carbon\Carbon::parse($pesanan->tanggal_pesan)->format('d F Y') }}
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('pesanan.index') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-800/80">
                Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
        <x-common.flash-alert type="success" :message="session('success')" />
    @endif
    @if(session('error'))
        <x-common.flash-alert type="error" :message="session('error')" />
    @endif
    @if($errors->any())
        <div class="rounded-lg bg-error-50 p-4 text-error-800 border border-error-200 dark:bg-error-900/20 dark:text-error-400 dark:border-error-800/30">
            <ul class="list-disc pl-5 text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Informasi Pesanan + Item Detail -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Info Utama -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-sm dark:border-gray-800 dark:bg-gray-dark">
                <h3 class="mb-5 text-lg font-bold text-gray-800 dark:text-white/90">Informasi Pesanan</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Kode Pesanan</p>
                        <p class="font-semibold text-gray-800 dark:text-white/90 font-mono">{{ $pesanan->kode_pesanan }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Tanggal Pesan</p>
                        <p class="font-medium text-gray-800 dark:text-white/90">{{ \Carbon\Carbon::parse($pesanan->tanggal_pesan)->format('d F Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Supplier</p>
                        <p class="font-medium text-gray-800 dark:text-white/90">{{ $pesanan->supplier->nama_supplier ?? '-' }}</p>
                        @if($pesanan->supplier && $pesanan->supplier->kontak)
                            <p class="text-xs text-gray-400">{{ $pesanan->supplier->kontak }}</p>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Status Saat Ini</p>
                        @php
                            $statusClass = [
                                'draft'    => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
                                'diproses' => 'bg-warning-100 text-warning-700 dark:bg-warning-900/30 dark:text-warning-400',
                                'dikirim'  => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                'selesai'  => 'bg-success-100 text-success-700 dark:bg-success-900/30 dark:text-success-400',
                                'batal'    => 'bg-error-100 text-error-700 dark:bg-error-900/30 dark:text-error-400',
                            ];
                            $class = $statusClass[$pesanan->status] ?? 'bg-gray-100 text-gray-700';
                        @endphp
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $class }}">
                            {{ ucfirst($pesanan->status) }}
                        </span>
                    </div>
                    @if($pesanan->catatan)
                    <div class="sm:col-span-2">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Catatan</p>
                        <p class="text-gray-700 dark:text-gray-300">{{ $pesanan->catatan }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Daftar Item Pesanan -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-sm dark:border-gray-800 dark:bg-gray-dark">
                <h3 class="mb-5 text-lg font-bold text-gray-800 dark:text-white/90">Item Pesanan</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/20">
                                <th class="px-4 py-3 font-medium text-gray-500 dark:text-gray-400">Obat</th>
                                <th class="px-4 py-3 font-medium text-gray-500 dark:text-gray-400 text-right">Jumlah Pesan</th>
                                <th class="px-4 py-3 font-medium text-gray-500 dark:text-gray-400 text-right">Est. Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pesanan->detailPesanan as $detail)
                                <tr class="border-b border-gray-50 dark:border-gray-800/50">
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-800 dark:text-white/90">{{ $detail->obat->nama_obat ?? '-' }}</div>
                                        <div class="text-xs text-gray-400">{{ $detail->obat->kode_obat ?? '' }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold text-brand-600 dark:text-brand-400">
                                        {{ $detail->jumlah_pesan }} {{ $detail->obat->satuan_beli ?? 'Box' }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400">
                                        @if($detail->estimasi_harga > 0)
                                            Rp {{ number_format($detail->estimasi_harga, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-6 text-center text-gray-400">Tidak ada item pesanan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Sidebar: Update Status / Terima Pesanan -->
        <div class="lg:col-span-1 space-y-4">

            @if($pesanan->status === 'selesai' || $pesanan->status === 'batal')
                <!-- Terminal state -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-sm dark:border-gray-800 dark:bg-gray-dark">
                    <div class="rounded-lg bg-gray-50 p-4 border border-gray-200 dark:bg-gray-800/50 dark:border-gray-700">
                        <p class="text-sm text-gray-600 dark:text-gray-400 text-center">
                            Pesanan ini sudah <b>{{ $pesanan->status }}</b> dan tidak dapat diubah lagi.
                        </p>
                    </div>
                </div>

            @elseif($pesanan->status === 'dikirim')
                <!-- ─── TERIMA PESANAN (barang tiba) ─── -->
                <div class="rounded-2xl border border-success-200 bg-white p-6 shadow-theme-sm dark:border-success-800/40 dark:bg-gray-dark">
                    <div class="flex items-center gap-2 mb-5">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-success-100 dark:bg-success-900/30">
                            <svg class="h-4 w-4 text-success-600 dark:text-success-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-gray-800 dark:text-white/90">Terima Pesanan</h3>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-5">
                        Isi detail penerimaan per item obat. Stok akan langsung masuk ke gudang.
                    </p>

                    <form action="{{ route('pesanan.terima', $pesanan->id) }}" method="POST" class="space-y-5">
                        @csrf
                        @foreach($pesanan->detailPesanan as $i => $detail)
                            <input type="hidden" name="items[{{ $i }}][detail_id]" value="{{ $detail->id }}">
                            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4 dark:border-gray-700/50 dark:bg-gray-800/30 space-y-3">
                                <p class="text-xs font-semibold text-gray-600 dark:text-gray-300 truncate">
                                    {{ $detail->obat->nama_obat ?? 'Obat' }}
                                    <span class="font-normal text-gray-400">(Dipesan: {{ $detail->jumlah_pesan }} {{ $detail->obat->satuan_beli ?? 'Box' }})</span>
                                </p>

                                <!-- Jumlah Diterima -->
                                <div>
                                    <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">
                                        Jumlah Diterima <span class="text-error-500">*</span>
                                    </label>
                                    <input type="number"
                                        name="items[{{ $i }}][jumlah_diterima]"
                                        value="{{ old("items.{$i}.jumlah_diterima", $detail->jumlah_pesan) }}"
                                        min="1" required
                                        class="h-9 w-full rounded-lg border border-gray-200 bg-white px-3 text-sm text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 transition">
                                </div>

                                <!-- Tanggal Kadaluwarsa -->
                                <div>
                                    <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">
                                        Tanggal Kadaluwarsa (ED) <span class="text-error-500">*</span>
                                    </label>
                                    <input type="date"
                                        name="items[{{ $i }}][tanggal_kadaluwarsa]"
                                        value="{{ old("items.{$i}.tanggal_kadaluwarsa") }}"
                                        min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                        required
                                        class="h-9 w-full rounded-lg border border-gray-200 bg-white px-3 text-sm text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 transition">
                                </div>

                                <!-- Harga Beli Satuan -->
                                <div>
                                    <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">
                                        Harga Beli / {{ $detail->obat->satuan_beli ?? 'Box' }} (Rp) <span class="text-error-500">*</span>
                                    </label>
                                    <input type="number"
                                        name="items[{{ $i }}][harga_beli_satuan]"
                                        value="{{ old("items.{$i}.harga_beli_satuan") }}"
                                        min="0" required
                                        placeholder="Contoh: 150000"
                                        class="h-9 w-full rounded-lg border border-gray-200 bg-white px-3 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-gray-500 transition">
                                </div>
                            </div>
                        @endforeach

                        <button type="submit"
                            onclick="return confirm('Konfirmasi penerimaan pesanan ini? Stok akan langsung masuk ke gudang.')"
                            class="h-11 w-full inline-flex items-center justify-center gap-2 rounded-lg bg-success-500 px-5 text-sm font-semibold text-white shadow-theme-xs hover:bg-success-600 focus:outline-none focus:ring-2 focus:ring-success-500/30 transition duration-150">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Konfirmasi Penerimaan
                        </button>
                    </form>
                </div>

                <!-- Update Status (untuk ubah ke selain selesai) -->
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-sm dark:border-gray-800 dark:bg-gray-dark">
                    <h3 class="mb-4 text-sm font-bold text-gray-700 dark:text-gray-300">Ubah Status Lainnya</h3>
                    <form action="{{ route('pesanan.updateStatus', $pesanan->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="relative mb-3">
                            <select name="status" required
                                class="h-10 w-full appearance-none rounded-lg border border-gray-200 bg-transparent px-3 pr-9 text-sm text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 transition cursor-pointer">
                                <option value="dikirim" selected class="dark:bg-gray-900">Dikirim (Dalam Perjalanan)</option>
                                <option value="batal" class="dark:bg-gray-900">Batal</option>
                            </select>
                            <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </span>
                        </div>
                        <button type="submit"
                            class="h-9 w-full inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-4 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition">
                            Simpan Perubahan
                        </button>
                    </form>
                </div>

            @else
                <!-- Status selain dikirim dan selesai/batal (draft, diproses) -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-sm dark:border-gray-800 dark:bg-gray-dark">
                    <h3 class="mb-5 text-lg font-bold text-gray-800 dark:text-white/90">Update Status</h3>

                    <form action="{{ route('pesanan.updateStatus', $pesanan->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="space-y-4">
                            <div>
                                <label for="status" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Pilih Status Baru</label>
                                <div class="relative">
                                    <select id="status" name="status" required
                                        class="h-11 w-full appearance-none rounded-lg border border-gray-200 bg-transparent px-4 py-2.5 pr-10 text-sm text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-500 transition duration-150 cursor-pointer">
                                        <option value="draft" {{ $pesanan->status == 'draft' ? 'selected' : '' }} class="dark:bg-gray-900">Draft</option>
                                        <option value="diproses" {{ $pesanan->status == 'diproses' ? 'selected' : '' }} class="dark:bg-gray-900">Diproses Supplier</option>
                                        <option value="dikirim" {{ $pesanan->status == 'dikirim' ? 'selected' : '' }} class="dark:bg-gray-900">Dikirim (Dalam Perjalanan)</option>
                                        <option value="batal" {{ $pesanan->status == 'batal' ? 'selected' : '' }} class="dark:bg-gray-900">Batal</option>
                                    </select>
                                    <span class="pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </span>
                                </div>
                            </div>

                            <div class="pt-2">
                                <button type="submit"
                                    class="h-11 w-full inline-flex items-center justify-center rounded-lg bg-brand-500 px-5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-500/30 transition duration-150">
                                    Simpan Perubahan
                                </button>
                            </div>

                            <hr class="border-gray-100 dark:border-gray-800" />

                            <div class="rounded-lg bg-blue-50 p-3 border border-blue-200 dark:bg-blue-900/20 dark:border-blue-800/30">
                                <p class="text-xs text-blue-700 dark:text-blue-400">
                                    <b>Info:</b> Ubah status ke <b>Dikirim</b> agar tombol
                                    <b>Terima Pesanan</b> muncul saat barang tiba.
                                </p>
                            </div>
                        </div>
                    </form>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection
