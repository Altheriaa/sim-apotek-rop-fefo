<?php

namespace App\Services;

use App\Models\Obat;
use App\Models\ObatBatch;
use App\Models\TransferRak;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\Notifikasi;
use App\Jobs\KirimNotifikasiWhatsapp;
use Illuminate\Support\Facades\DB;
use Exception;

class StokService
{
    // ══════════════════════════════════════════════════════════════════
    // Helper: Hitung Total Stok Apotek dalam Satuan Jual
    // ══════════════════════════════════════════════════════════════════

    /**
     * Total Stok (satuan_jual) = (stok_gudang × isi_per_kemasan) + stok_rak
     */
    public function hitungTotalStokSatuanJual(Obat $obat): int
    {
        $stokGudang = $obat->batches()->sum('stok_gudang');
        $stokRak    = $obat->batches()->sum('stok_rak');
        return ($stokGudang * $obat->isi_per_kemasan) + $stokRak;
    }

    // ══════════════════════════════════════════════════════════════════
    // 1. Transfer Stok dari Gudang ke Display Rak (FEFO + Konversi)
    // ══════════════════════════════════════════════════════════════════

    /**
     * Pindahkan sejumlah Box/Dus dari gudang ke rak display.
     * Konversi otomatis: $jumlahBox × isi_per_kemasan → stok_rak (satuan_jual).
     * Menggunakan algoritma FEFO: batch dengan ED terdekat dipindah lebih dulu.
     *
     * Contoh: Transfer 2 Box Paracetamol (isi_per_kemasan=10)
     *         → stok_gudang: -2 Box
     *         → stok_rak:    +20 Strip
     *
     * @throws Exception Jika stok gudang tidak mencukupi
     */
    public function transferKeRak(int $obatId, int $jumlahBox, int $userId, ?string $keterangan = null): array
    {
        return DB::transaction(function () use ($obatId, $jumlahBox, $userId, $keterangan) {
            $obat = Obat::findOrFail($obatId);
            $totalGudang = $obat->batches()->sum('stok_gudang');

            if ($totalGudang < $jumlahBox) {
                throw new Exception(
                    "Stok di gudang tidak mencukupi. Tersedia: {$totalGudang} {$obat->satuan_beli}"
                );
            }

            $sisaBox = $jumlahBox;
            $batchDipindahkan = [];

            // Ambil batch gudang urut ED terdekat (FEFO)
            $batches = ObatBatch::where('obat_id', $obatId)
                ->where('stok_gudang', '>', 0)
                ->orderBy('tanggal_kadaluwarsa', 'asc')
                ->lockForUpdate()
                ->get();

            foreach ($batches as $batch) {
                if ($sisaBox <= 0) break;

                $ambilBox        = min($batch->stok_gudang, $sisaBox);
                $hasilSatuanJual = $ambilBox * $obat->isi_per_kemasan;

                $batch->decrement('stok_gudang', $ambilBox);       // Kurangi gudang (Box)
                $batch->increment('stok_rak', $hasilSatuanJual);   // Tambah rak (Strip/Botol)

                TransferRak::create([
                    'obat_id'          => $obatId,
                    'obat_batch_id'    => $batch->id,
                    'user_id'          => $userId,
                    'jumlah_keluar'    => $ambilBox,
                    'jumlah_masuk_rak' => $hasilSatuanJual,
                    'tanggal_transfer' => now()->toDateString(),
                    'keterangan'       => $keterangan ?? "Transfer {$ambilBox} {$obat->satuan_beli} → {$hasilSatuanJual} {$obat->satuan_jual} ke display rak",
                ]);

                $batchDipindahkan[] = [
                    'nomor_batch'   => $batch->nomor_batch,
                    'jumlah_box'    => $ambilBox,
                    'jumlah_satuan' => $hasilSatuanJual,
                    'satuan_beli'   => $obat->satuan_beli,
                    'satuan_jual'   => $obat->satuan_jual,
                    'ed'            => $batch->tanggal_kadaluwarsa->format('d/m/Y'),
                ];

                $sisaBox -= $ambilBox;
            }

            return $batchDipindahkan;
        });
    }

    // ══════════════════════════════════════════════════════════════════
    // 2. Transaksi Kasir / POS (FEFO Rak) + Cek ROP Otomatis
    // ══════════════════════════════════════════════════════════════════

    /**
     * Proses checkout kasir. Mengurangi stok_rak via FEFO.
     * Kasir menjual dalam satuan_jual (Strip/Sachet/Botol).
     * Setiap item yang terjual dicek terhadap ROP & min stok rak.
     *
     * @param  array $dataTransaksi  ['total_harga', 'nominal_bayar', 'kembalian', 'nama_pembeli'?, 'catatan'?]
     * @param  array $items          [['obat_id', 'jumlah'], ...] — jumlah dalam satuan_jual
     * @throws Exception Jika stok rak tidak mencukupi
     */
    public function prosesPenjualan(array $dataTransaksi, array $items, int $userId): Penjualan
    {
        return DB::transaction(function () use ($dataTransaksi, $items, $userId) {
            $noTransaksi = 'TRX-' . date('Ymd') . '-' . str_pad(
                Penjualan::whereDate('tanggal_transaksi', today())->count() + 1,
                4, '0', STR_PAD_LEFT
            );

            $penjualan = Penjualan::create([
                'no_transaksi'      => $noTransaksi,
                'user_id'           => $userId,
                'tanggal_transaksi' => now(),
                'total_harga'       => $dataTransaksi['total_harga'],
                'nominal_bayar'     => $dataTransaksi['nominal_bayar'],
                'kembalian'         => $dataTransaksi['kembalian'],
                'nama_pembeli'      => $dataTransaksi['nama_pembeli'] ?? 'Umum',
                'catatan'           => $dataTransaksi['catatan'] ?? null,
            ]);

            foreach ($items as $item) {
                $obat    = Obat::findOrFail($item['obat_id']);
                $qtyBeli = (int) $item['jumlah']; // Dalam satuan_jual (misal 3 Strip)

                $stokRakTersedia = $obat->batches()->sum('stok_rak');

                if ($stokRakTersedia < $qtyBeli) {
                    throw new Exception(
                        "Stok rak obat \"{$obat->nama_obat}\" tidak mencukupi " .
                        "(Tersisa di rak: {$stokRakTersedia} {$obat->satuan_jual}). " .
                        "Silakan lakukan Transfer dari Gudang terlebih dahulu."
                    );
                }

                $sisa = $qtyBeli;

                // Kurangi dari batch rak yang ED-nya paling dekat (FEFO Rak)
                $batchesRak = ObatBatch::where('obat_id', $obat->id)
                    ->where('stok_rak', '>', 0)
                    ->orderBy('tanggal_kadaluwarsa', 'asc')
                    ->lockForUpdate()
                    ->get();

                foreach ($batchesRak as $batch) {
                    if ($sisa <= 0) break;

                    $ambil = min($batch->stok_rak, $sisa);
                    $batch->decrement('stok_rak', $ambil);

                    DetailPenjualan::create([
                        'penjualan_id'  => $penjualan->id,
                        'obat_id'       => $obat->id,
                        'obat_batch_id' => $batch->id,
                        'jumlah'        => $ambil,                       // Dalam satuan_jual
                        'harga_satuan'  => $obat->harga_jual,            // Harga per satuan_jual
                        'subtotal'      => $ambil * $obat->harga_jual,
                    ]);

                    $sisa -= $ambil;
                }

                // Evaluasi ROP & status rak setelah setiap item terjual
                $this->cekRopDanRak($obat->fresh());
            }

            return $penjualan;
        });
    }

    // ══════════════════════════════════════════════════════════════════
    // 3. Pengecekan Otomatis ROP (Total Apotek dalam Satuan Jual) & Rak
    // ══════════════════════════════════════════════════════════════════

    /**
     * Evaluasi dua kondisi peringatan stok:
     * A. Total Stok Apotek (satuan_jual) ≤ (ROP × isi_per_kemasan) → Kirim notifikasi WA, suggest pesanan
     * B. Stok Rak ≤ min_stok_rak → Log notifikasi restock_rak
     *
     * rop_minimum didefinisikan dalam satuan_beli (misal: 5 Box)
     * Total Stok (satuan_jual) = (stok_gudang × isi_per_kemasan) + stok_rak
     */
    public function cekRopDanRak(Obat $obat): void
    {
        $stokGudang         = $obat->batches()->sum('stok_gudang'); // dalam satuan_beli (Box)
        $stokRak            = $obat->batches()->sum('stok_rak');     // dalam satuan_jual (Strip)
        $totalSatuanJual    = ($stokGudang * $obat->isi_per_kemasan) + $stokRak;
        $batasRopSatuanJual = $obat->rop_minimum * $obat->isi_per_kemasan; // ROP Box dinormalisasi ke satuan jual

        // A. Pemicu ROP (Total Apotek ≤ ROP Minimum dalam satuan_beli)
        if ($obat->rop_minimum > 0 && $totalSatuanJual <= $batasRopSatuanJual) {
            $sudahAda = Notifikasi::where('obat_id', $obat->id)
                ->where('jenis_notifikasi', 'stok_menipis')
                ->whereDate('created_at', today())
                ->exists();

            if (! $sudahAda) {
                $gudangDlmJual = $stokGudang * $obat->isi_per_kemasan;
                $pesan = "*PERINGATAN ROP — Stok Menipis*\n"
                    . "Obat: *{$obat->nama_obat}*\n"
                    . "Total Apotek: *{$totalSatuanJual} {$obat->satuan_jual}*\n"
                    . "  → Gudang: {$stokGudang} {$obat->satuan_beli} (= {$gudangDlmJual} {$obat->satuan_jual})\n"
                    . "  → Rak: {$stokRak} {$obat->satuan_jual}\n"
                    . "Batas ROP: *{$obat->rop_minimum} {$obat->satuan_beli}*" . ($obat->isi_per_kemasan > 1 ? " (= {$batasRopSatuanJual} {$obat->satuan_jual})" : "") . "\n"
                    . "Segera lakukan pemesanan ulang ke supplier.";

                $notif = Notifikasi::create([
                    'obat_id'          => $obat->id,
                    'jenis_notifikasi' => 'stok_menipis',
                    'pesan'            => $pesan,
                    'target_nomor'     => config('services.fonnte.admin_target', '08974688919'),
                    'status'           => 'pending',
                ]);

                KirimNotifikasiWhatsapp::dispatch($notif);
            }
        }

        // B. Peringatan Rak Kosong (stok_rak ≤ min_stok_rak, dalam satuan_jual)
        if ($obat->min_stok_rak > 0 && $stokRak <= $obat->min_stok_rak && $stokGudang > 0) {
            $sudahAda = Notifikasi::where('obat_id', $obat->id)
                ->where('jenis_notifikasi', 'restock_rak')
                ->whereDate('created_at', today())
                ->exists();

            if (! $sudahAda) {
                Notifikasi::create([
                    'obat_id'          => $obat->id,
                    'jenis_notifikasi' => 'restock_rak',
                    'pesan'            => "📦 Stok rak obat *{$obat->nama_obat}* menipis ({$stokRak} {$obat->satuan_jual}). Gudang masih ada {$stokGudang} {$obat->satuan_beli}. Silakan lakukan Transfer ke Rak.",
                    'target_nomor'     => config('services.fonnte.admin_target', '08974688919'),
                    'status'           => 'pending',
                ]);
            }
        }
    }

    // ══════════════════════════════════════════════════════════════════
    // 4. Pengecekan Kadaluwarsa (Scheduler Harian)
    // ══════════════════════════════════════════════════════════════════

    /**
     * Cek batch obat yang mendekati kadaluwarsa (≤ $days hari).
     * Dipanggil oleh scheduled command harian.
     */
    public function cekKadaluwarsa(int $days = 30): int
    {
        $batches = ObatBatch::with('obat')
            ->where(function ($q) {
                $q->where('stok_gudang', '>', 0)->orWhere('stok_rak', '>', 0);
            })
            ->whereBetween('tanggal_kadaluwarsa', [now()->toDateString(), now()->addDays($days)->toDateString()])
            ->get();

        $count = 0;

        foreach ($batches as $batch) {
            $sudahAda = Notifikasi::where('obat_id', $batch->obat_id)
                ->where('jenis_notifikasi', 'mendekati_kadaluwarsa')
                ->whereDate('created_at', today())
                ->exists();

            if ($sudahAda) continue;

            $sisaHari  = now()->diffInDays($batch->tanggal_kadaluwarsa);

            $notif = Notifikasi::create([
                'obat_id'          => $batch->obat_id,
                'jenis_notifikasi' => 'mendekati_kadaluwarsa',
                'pesan'            => "⏰ *MENDEKATI KADALUWARSA*\n"
                    . "Obat: *{$batch->obat->nama_obat}*\n"
                    . "No. Batch: {$batch->nomor_batch}\n"
                    . "ED: {$batch->tanggal_kadaluwarsa->format('d/m/Y')} ({$sisaHari} hari lagi)\n"
                    . "Sisa Stok Batch: Gudang {$batch->stok_gudang} {$batch->obat->satuan_beli} | Rak {$batch->stok_rak} {$batch->obat->satuan_jual}",
                'target_nomor'     => config('services.fonnte.admin_target', '08974688919'),
                'status'           => 'pending',
            ]);

            KirimNotifikasiWhatsapp::dispatch($notif);
            $count++;
        }

        return $count;
    }
}
