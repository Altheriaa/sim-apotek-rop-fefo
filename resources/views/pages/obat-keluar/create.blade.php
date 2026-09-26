@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white/90">Catat Pembuangan Obat</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Catat batch obat yang dibuang karena expired atau rusak. Stok akan otomatis berkurang.</p>
        </div>
        <a href="{{ route('obat-keluar.index') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 transition">
            ← Kembali
        </a>
    </div>

    <!-- Alert -->
    @if(session('error'))
        <x-common.flash-alert type="error" :message="session('error')" />
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Form Disposal -->
        <div class="lg:col-span-2">
            <div class="rounded-xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-gray-dark">
                <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-800">
                    <h2 class="text-base font-semibold text-gray-800 dark:text-white/90">Form Pembuangan Batch</h2>
                </div>
                <form action="{{ route('obat-keluar.store') }}" method="POST" class="p-5 space-y-5">
                    @csrf

                    <!-- Pilih Batch -->
                    <div>
                        <label for="obat_batch_id" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Pilih Batch Obat <span class="text-red-500">*</span>
                        </label>
                        <select id="obat_batch_id" name="obat_batch_id" required onchange="updateBatchInfo(this)"
                                class="h-11 w-full rounded-lg border border-gray-200 bg-gray-50/50 px-3.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900/60 dark:text-white/90 transition duration-150 @error('obat_batch_id') border-red-500 @enderror">
                            <option value="">-- Pilih Batch --</option>
                            @foreach($batches as $batch)
                                @php
                                    $isExpired = $batch->tanggal_kadaluwarsa->lt(now());
                                    $label = $batch->obat->nama_obat . ' (' . $batch->nomor_batch . ')'
                                           . ' - ED: ' . $batch->tanggal_kadaluwarsa->format('d/m/Y')
                                           . ($isExpired ? ' ⚠ EXPIRED' : '');
                                @endphp
                                <option value="{{ $batch->id }}"
                                        data-stok-gudang="{{ $batch->stok_gudang }}"
                                        data-stok-rak="{{ $batch->stok_rak }}"
                                        data-satuan-beli="{{ $batch->obat->satuan_beli }}"
                                        data-satuan-jual="{{ $batch->obat->satuan_jual }}"
                                        data-expired="{{ $isExpired ? '1' : '0' }}"
                                        {{ old('obat_batch_id') == $batch->id ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('obat_batch_id')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Info Stok Batch (dinamis via JS) -->
                    <div id="batch-info" class="hidden rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
                        <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-blue-600 dark:text-blue-400">Stok Saat Ini pada Batch</p>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="rounded-md bg-white p-3 dark:bg-gray-900/50">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Stok Gudang</p>
                                <p id="info-gudang" class="text-lg font-bold text-gray-800 dark:text-white">—</p>
                            </div>
                            <div class="rounded-md bg-white p-3 dark:bg-gray-900/50">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Stok Rak</p>
                                <p id="info-rak" class="text-lg font-bold text-gray-800 dark:text-white">—</p>
                            </div>
                        </div>
                    </div>

                    <!-- Jumlah Buang -->
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="jumlah_gudang" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Buang dari Gudang <span id="satuan-beli-label" class="text-gray-400 text-xs"></span>
                            </label>
                            <input type="number" id="jumlah_gudang" name="jumlah_gudang"
                                   value="{{ old('jumlah_gudang', 0) }}" min="0"
                                   class="h-11 w-full rounded-lg border border-gray-200 bg-gray-50/50 px-3.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900/60 dark:text-white/90 transition @error('jumlah_gudang') border-red-500 @enderror">
                            @error('jumlah_gudang')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="jumlah_rak" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Buang dari Rak <span id="satuan-jual-label" class="text-gray-400 text-xs"></span>
                            </label>
                            <input type="number" id="jumlah_rak" name="jumlah_rak"
                                   value="{{ old('jumlah_rak', 0) }}" min="0"
                                   class="h-11 w-full rounded-lg border border-gray-200 bg-gray-50/50 px-3.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900/60 dark:text-white/90 transition @error('jumlah_rak') border-red-500 @enderror">
                            @error('jumlah_rak')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Alasan -->
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Alasan Pembuangan <span class="text-red-500">*</span>
                        </label>
                        <div class="flex flex-wrap gap-3">
                            @foreach($alasanOptions as $key => $label)
                                <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-gray-200 px-4 py-2.5 text-sm transition hover:border-brand-400 dark:border-gray-700 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50 dark:has-[:checked]:border-brand-500 dark:has-[:checked]:bg-brand-900/20">
                                    <input type="radio" name="alasan" value="{{ $key }}" {{ old('alasan', 'expired') === $key ? 'checked' : '' }}
                                           class="h-4 w-4 accent-brand-500">
                                    <span class="font-medium text-gray-700 dark:text-gray-300">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('alasan')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Catatan -->
                    <div>
                        <label for="catatan" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Catatan (Opsional)</label>
                        <textarea id="catatan" name="catatan" rows="3" placeholder="Misal: Ditemukan saat audit stok tanggal XX/XX/XXXX"
                                  class="w-full rounded-lg border border-gray-200 bg-gray-50/50 px-3.5 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900/60 dark:text-white/90 transition @error('catatan') border-red-500 @enderror">{{ old('catatan') }}</textarea>
                        @error('catatan')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <a href="{{ route('obat-keluar.index') }}" class="rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 transition">
                            Batal
                        </a>
                        <button type="submit"
                                class="rounded-lg bg-red-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-red-600 transition">
                            <svg class="mr-1.5 -ml-0.5 inline h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Konfirmasi Pembuangan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Info Panel -->
        <div class="space-y-4">
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 dark:border-amber-800 dark:bg-amber-900/20">
                <h3 class="mb-2 flex items-center gap-2 text-sm font-semibold text-amber-800 dark:text-amber-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    Perhatian
                </h3>
                <ul class="space-y-1.5 text-xs text-amber-700 dark:text-amber-300">
                    <li>• Tindakan ini <strong>tidak dapat dibatalkan</strong>. Stok akan langsung berkurang.</li>
                    <li>• Pastikan nomor batch dan jumlah yang dibuang sudah benar.</li>
                    <li>• Isi catatan jika ada informasi tambahan yang relevan.</li>
                </ul>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-dark">
                <h3 class="mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">Alasan Pembuangan</h3>
                <ul class="space-y-2 text-xs text-gray-600 dark:text-gray-400">
                    <li class="flex items-start gap-2">
                        <span class="mt-0.5 rounded-full bg-red-100 px-2 py-0.5 text-red-700 dark:bg-red-900/30 dark:text-red-400">Expired</span>
                        <span>Tanggal kadaluwarsa sudah terlewati.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="mt-0.5 rounded-full bg-orange-100 px-2 py-0.5 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400">Rusak</span>
                        <span>Kemasan rusak, terkontaminasi, atau tidak layak pakai.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="mt-0.5 rounded-full bg-gray-100 px-2 py-0.5 text-gray-700 dark:bg-gray-800 dark:text-gray-400">Lainnya</span>
                        <span>Alasan lain: isi catatan untuk penjelasan.</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
function updateBatchInfo(select) {
    const opt = select.options[select.selectedIndex];
    const info = document.getElementById('batch-info');
    const infoGudang = document.getElementById('info-gudang');
    const infoRak = document.getElementById('info-rak');
    const satuanBeli = document.getElementById('satuan-beli-label');
    const satuanJual = document.getElementById('satuan-jual-label');

    if (!opt.value) {
        info.classList.add('hidden');
        return;
    }

    const stokGudang = opt.dataset.stokGudang;
    const stokRak = opt.dataset.stokRak;
    const sb = opt.dataset.satuanBeli;
    const sj = opt.dataset.satuanJual;

    infoGudang.textContent = stokGudang + ' ' + sb;
    infoRak.textContent = stokRak + ' ' + sj;
    satuanBeli.textContent = '(' + sb + ')';
    satuanJual.textContent = '(' + sj + ')';

    document.getElementById('jumlah_gudang').max = stokGudang;
    document.getElementById('jumlah_rak').max = stokRak;

    // Auto-set alasan ke expired jika batch memang sudah expired
    if (opt.dataset.expired === '1') {
        const radio = document.querySelector('input[name="alasan"][value="expired"]');
        if (radio) radio.checked = true;
    }

    info.classList.remove('hidden');
}

// Trigger on page load jika ada old value
document.addEventListener('DOMContentLoaded', function () {
    const sel = document.getElementById('obat_batch_id');
    if (sel.value) updateBatchInfo(sel);
});
</script>
@endsection
