{{-- Notification Dropdown Component --}}
<div class="relative" x-data="{
    dropdownOpen: false,
    notifying: {{ $unreadCount > 0 ? 'true' : 'false' }},
    toggleDropdown() {
        this.dropdownOpen = !this.dropdownOpen;
        if (this.dropdownOpen) {
            this.notifying = false;
        }
    },
    closeDropdown() {
        this.dropdownOpen = false;
    }
}" @click.away="closeDropdown()">
    <!-- Notification Button -->
    <button
        class="relative flex items-center justify-center text-gray-500 transition-colors bg-white border border-gray-200 rounded-full hover:text-dark-900 h-11 w-11 hover:bg-gray-100 hover:text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
        @click="toggleDropdown()"
        type="button"
        aria-label="Notifikasi"
    >
        @if($unreadCount > 0)
            <!-- Notification Badge with Count -->
            <span
                x-show="notifying"
                class="absolute -top-1 -right-1 z-10 flex h-5 min-w-[20px] items-center justify-center rounded-full bg-error-500 px-1 text-[11px] font-bold text-white shadow-sm ring-2 ring-white dark:ring-gray-900"
            >
                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-error-400 opacity-75 -z-1"></span>
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif

        <!-- Bell Icon -->
        <i class="ti ti-bell text-xl"></i>
    </button>

    <!-- Dropdown Start -->
    <div
        x-show="dropdownOpen"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute -right-[240px] mt-[17px] flex h-auto max-h-[520px] w-[350px] flex-col rounded-2xl border border-gray-200 bg-white p-3 shadow-theme-lg dark:border-gray-800 dark:bg-gray-dark sm:w-[380px] lg:right-0 z-50"
        style="display: none;"
    >
        <!-- Dropdown Header -->
        <div class="flex items-center justify-between pb-3 mb-2 border-b border-gray-100 dark:border-gray-800">
            <div class="flex items-center gap-2">
                <h5 class="text-base font-semibold text-gray-800 dark:text-white/90">Notifikasi</h5>
                @if($unreadCount > 0)
                    <span class="rounded-full bg-brand-50 px-2 py-0.5 text-xs font-semibold text-brand-500 dark:bg-brand-500/15 dark:text-brand-400">
                        {{ $unreadCount }} Hari Ini
                    </span>
                @endif
            </div>

            <button @click="closeDropdown()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition" type="button" aria-label="Tutup">
                <i class="ti ti-x text-lg"></i>
            </button>
        </div>

        <!-- Notification List -->
        <ul class="flex flex-col h-auto max-h-[360px] overflow-y-auto divide-y divide-gray-100 dark:divide-gray-800/60 custom-scrollbar pr-1">
            @forelse ($notifications as $notif)
                @php
                    $isRop = $notif->jenis_notifikasi === 'stok_menipis';
                    $isRak = $notif->jenis_notifikasi === 'restock_rak';
                    $isEd  = in_array($notif->jenis_notifikasi, ['mendekati_kadaluwarsa', 'kadaluwarsa']);
                    $userRole = auth()->user()?->role ?? 'karyawan';

                    // Route target sesuai role dan jenis notifikasi
                    $targetUrl = route('dashboard');
                    if ($notif->obat_id) {
                        if ($isRop) {
                            $targetUrl = in_array($userRole, ['admin', 'owner'])
                                ? route('pesanan.create', ['obat_id' => $notif->obat_id])
                                : route('stok-gudang.index', ['search' => $notif->obat?->nama_obat]);
                        } elseif ($isRak) {
                            $targetUrl = in_array($userRole, ['admin', 'karyawan'])
                                ? route('transfer-rak.create', ['obat_id' => $notif->obat_id])
                                : route('display-rak.index');
                        } else {
                            $targetUrl = route('stok-gudang.index', ['search' => $notif->obat?->nama_obat]);
                        }
                    }
                @endphp
                <li class="py-2.5 first:pt-1 last:pb-1">
                    <a
                        href="{{ $targetUrl }}"
                        @click="closeDropdown()"
                        class="flex items-start gap-3 p-2 rounded-xl transition hover:bg-gray-50 dark:hover:bg-white/5 group"
                    >
                        <!-- Icon Box -->
                        <div class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $isRop ? 'bg-amber-50 text-amber-600 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-900/50' : ($isRak ? 'bg-blue-50 text-blue-600 border border-blue-200 dark:bg-blue-950/40 dark:text-blue-400 dark:border-blue-900/50' : 'bg-rose-50 text-rose-600 border border-rose-200 dark:bg-rose-950/40 dark:text-rose-400 dark:border-rose-900/50') }}">
                            <i class="ti {{ $isRop ? 'ti-alert-triangle' : ($isRak ? 'ti-arrow-right-circle' : 'ti-calendar-due') }} text-lg"></i>
                        </div>

                        <!-- Content -->
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-1 mb-0.5">
                                <span class="text-xs font-semibold {{ $isRop ? 'text-amber-700 dark:text-amber-400' : ($isRak ? 'text-blue-700 dark:text-blue-400' : 'text-rose-700 dark:text-rose-400') }}">
                                    {{ $notif->judul }}
                                </span>
                                <span class="text-[10px] text-gray-400 shrink-0">
                                    {{ $notif->created_at->diffForHumans(null, true) }}
                                </span>
                            </div>

                            @if($notif->obat)
                                <p class="text-xs font-medium text-gray-800 dark:text-white/90 truncate group-hover:text-brand-500 transition">
                                    {{ $notif->obat->nama_obat }}
                                </p>
                            @endif

                            <p class="text-[11px] text-gray-500 dark:text-gray-400 line-clamp-2 leading-relaxed mt-0.5">
                                {{ $notif->pesan_rapi }}
                            </p>

                            <!-- Badge Status Tag -->
                            <div class="mt-1.5 flex items-center gap-1.5">
                                @if($isRak)
                                    <span class="inline-flex items-center text-[10px] font-medium text-blue-600 bg-blue-50 dark:bg-blue-900/30 dark:text-blue-400 px-1.5 py-0.5 rounded">
                                        <i class="ti ti-building-warehouse mr-1 text-[11px]"></i> Rak Display
                                    </span>
                                @elseif($notif->isTerkirim())
                                    <span class="inline-flex items-center text-[10px] font-medium text-emerald-600 bg-emerald-50 dark:bg-emerald-900/30 dark:text-emerald-400 px-1.5 py-0.5 rounded">
                                        <i class="ti ti-brand-whatsapp mr-1 text-[11px]"></i> WA Terkirim
                                    </span>
                                @elseif($notif->isPending())
                                    <span class="inline-flex items-center text-[10px] font-medium text-amber-600 bg-amber-50 dark:bg-amber-900/30 dark:text-amber-400 px-1.5 py-0.5 rounded">
                                        <i class="ti ti-clock mr-1 text-[11px]"></i> WA Pending
                                    </span>
                                @else
                                    <span class="inline-flex items-center text-[10px] font-medium text-red-600 bg-red-50 dark:bg-red-900/30 dark:text-red-400 px-1.5 py-0.5 rounded">
                                        <i class="ti ti-alert-circle mr-1 text-[11px]"></i> WA Gagal
                                    </span>
                                @endif
                            </div>
                        </div>
                    </a>
                </li>
            @empty
                <li class="py-8 flex flex-col items-center justify-center text-center">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-400 dark:bg-gray-800 dark:text-gray-500 mb-2">
                        <i class="ti ti-bell-off text-2xl"></i>
                    </div>
                    <p class="text-xs font-medium text-gray-600 dark:text-gray-300">Tidak ada notifikasi baru</p>
                    <p class="text-[11px] text-gray-400 mt-0.5">Stok dan kadaluwarsa aman terkendali</p>
                </li>
            @endforelse
        </ul>

        <!-- View All Button -->
        <div class="mt-3 pt-2 border-t border-gray-100 dark:border-gray-800">
            <a
                href="{{ route('dashboard') }}#notifikasi-widget"
                @click="closeDropdown()"
                class="flex items-center justify-center gap-1.5 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-100 hover:text-gray-900 dark:border-gray-800 dark:bg-gray-800/70 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white transition"
            >
                <span>Lihat Semua di Dashboard</span>
                <i class="ti ti-arrow-right text-xs"></i>
            </a>
        </div>
    </div>
    <!-- Dropdown End -->
</div>
