@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white/90">Transfer Stok ke Rak</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Pindahkan stok dari gudang ke display rak. Sistem akan
                    otomatis menggunakan FEFO (ED terdekat dipindah lebih dulu).</p>
            </div>
            <a href="{{ route('transfer-rak.index') }}"
                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                <i class="ti ti-arrow-left text-base"></i>
                Kembali
            </a>
        </div>

        @if(session('error'))
            <div
                class="rounded-lg bg-error-50 p-4 text-error-800 border border-error-200 dark:bg-error-900/20 dark:text-error-400 dark:border-error-800/30">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            {{-- Form Card --}}
            <div class="lg:col-span-2">
                <div
                    class="rounded-xl border border-gray-200 bg-white shadow-theme-xs dark:border-gray-800 dark:bg-gray-dark">
                    <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-800">
                        <h2 class="text-base font-semibold text-gray-800 dark:text-white/90">Form Transfer ke Rak</h2>
                    </div>
                    <form action="{{ route('transfer-rak.store') }}" method="POST" class="p-5 space-y-5">
                        @csrf

                        {{-- Pilih Obat --}}
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Pilih Obat <span class="text-error-500">*</span>
                            </label>
                            <div class="relative">
                                <select name="obat_id" id="obat_id" required
                                    class="h-11 w-full appearance-none rounded-lg border border-gray-200 bg-white pl-3.5 pr-10 text-sm text-gray-700 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 transition cursor-pointer @error('obat_id') border-error-500 @enderror">
                                    <option value="">-- Pilih Obat yang Ada di Gudang --</option>
                                    @foreach($obats as $obat)
                                        @php $stokGudang = $obat->batches->sum('stok_gudang'); @endphp
                                        <option value="{{ $obat->id }}" data-stok="{{ $stokGudang }}"
                                            data-satuan="{{ $obat->satuan }}"
                                            data-batches="{{ $obat->batches->map(fn($b) => ['batch' => $b->nomor_batch, 'stok' => $b->stok_gudang, 'ed' => $b->tanggal_kadaluwarsa->format('d/m/Y')])->toJson() }}"
                                            {{ old('obat_id', request('obat_id')) == $obat->id ? 'selected' : '' }}>
                                            {{ $obat->nama_obat }} — Gudang: {{ $stokGudang }} {{ $obat->satuan }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </span>
                            </div>
                            @error('obat_id')
                            <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
                        </div>

                        {{-- Info Stok Gudang (FEFO preview) --}}
                        <div id="fefo-preview"
                            class="hidden rounded-lg border border-blue-100 bg-blue-50/70 p-4 dark:border-blue-900/20 dark:bg-blue-900/10">
                            <div class="mb-2 flex items-center gap-1.5">
                                <i class="ti ti-info-circle text-blue-500 text-sm"></i>
                                <span class="text-xs font-semibold text-blue-600 dark:text-blue-400">Urutan FEFO Batch di
                                    Gudang</span>
                            </div>
                            <div id="fefo-list" class="space-y-1 text-xs text-blue-700 dark:text-blue-300"></div>
                        </div>

                        {{-- Jumlah --}}
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Jumlah Transfer <span class="text-error-500">*</span>
                            </label>
                            <div class="flex items-center gap-3">
                                <input type="number" name="jumlah" id="jumlah" min="1" value="{{ old('jumlah') }}" required
                                    class="h-11 flex-1 rounded-lg border border-gray-200 bg-white px-3.5 text-sm text-gray-700 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 transition @error('jumlah') border-error-500 @enderror"
                                    placeholder="Masukkan jumlah...">
                                <span id="satuan-label"
                                    class="text-sm text-gray-500 dark:text-gray-400 min-w-[3rem]"></span>
                            </div>
                            <p id="stok-info" class="mt-1 text-xs text-gray-400"></p>
                            @error('jumlah')
                            <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
                        </div>

                        {{-- Keterangan --}}
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Keterangan
                                (Opsional)</label>
                            <input type="text" name="keterangan" value="{{ old('keterangan') }}"
                                class="h-11 w-full rounded-lg border border-gray-200 bg-white px-3.5 text-sm text-gray-700 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 transition"
                                placeholder="Contoh: Restock rak pagi hari">
                        </div>

                        <div class="flex justify-end gap-3 pt-2">
                            <a href="{{ route('transfer-rak.index') }}"
                                class="h-11 inline-flex items-center rounded-lg border border-gray-200 px-6 text-sm font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800 transition">
                                Batal
                            </a>
                            <button type="submit"
                                class="h-11 inline-flex items-center gap-2 rounded-lg bg-brand-500 px-6 text-sm font-medium text-white hover:bg-brand-600 transition">
                                <i class="ti ti-circle-arrow-right text-base"></i>
                                Proses Transfer
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Info Panel --}}
            <div class="space-y-4">
                <div
                    class="rounded-xl border border-gray-200 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-gray-dark">
                    <h3 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Cara Kerja FEFO</h3>
                    <ol class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                        <li><span class="font-bold text-brand-500">1.</span> Sistem membaca semua batch
                            obat yang masih ada di gudang.</li>
                        <li><span class="font-bold text-brand-500">2.</span> Batch dengan tanggal
                            kadaluwarsa (ED) <strong>terdekat</strong> dipindahkan lebih dulu ke rak.</li>
                        <li><span class="font-bold text-brand-500">3.</span> Jika satu batch tidak cukup,
                            sistem otomatis melanjutkan ke batch berikutnya.</li>
                        <li><span class="font-bold text-brand-500">4.</span> Setiap pemindahan tercatat
                            di riwayat Transfer Rak.</li>
                    </ol>
                </div>
                <div
                    class="rounded-xl border border-warning-200 bg-warning-50/70 p-4 dark:border-warning-800/30 dark:bg-warning-900/10">
                    <p class="text-xs text-warning-700 dark:text-warning-400">
                        <strong>Perhatian:</strong> Pastikan rak sudah siap sebelum melakukan transfer. Stok yang sudah
                        dipindahkan ke rak tidak bisa dikembalikan ke gudang melalui sistem ini.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const select = document.getElementById('obat_id');
        const fefoPreview = document.getElementById('fefo-preview');
        const fefoList = document.getElementById('fefo-list');
        const stokInfo = document.getElementById('stok-info');
        const satuanLabel = document.getElementById('satuan-label');

        function updateInfo() {
            const option = select.options[select.selectedIndex];
            const stok = option.dataset.stok || 0;
            const satuan = option.dataset.satuan || '';
            const batches = option.dataset.batches ? JSON.parse(option.dataset.batches) : [];

            satuanLabel.textContent = satuan;
            stokInfo.textContent = stok > 0 ? `Stok gudang tersedia: ${stok} ${satuan}` : '';

            if (batches.length > 0) {
                fefoList.innerHTML = batches.map((b, i) =>
                    `<div class="flex items-center gap-2">
                                <span class="font-bold text-blue-600 dark:text-blue-400">${i + 1}.</span>
                                Batch <strong>${b.batch}</strong> — ED: ${b.ed} — Stok: ${b.stok} ${satuan}
                                ${i === 0 ? '<span class="ml-auto rounded-full bg-blue-200 dark:bg-blue-800 px-1.5 py-0.5 text-[10px] text-blue-700 dark:text-blue-300">Diambil Pertama</span>' : ''}
                            </div>`
                ).join('');
                fefoPreview.classList.remove('hidden');
            } else {
                fefoPreview.classList.add('hidden');
            }
        }

        select.addEventListener('change', updateInfo);
        if (select.value) updateInfo();
    </script>
@endpush