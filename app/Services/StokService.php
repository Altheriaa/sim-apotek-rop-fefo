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
    // 1. Transfer Stok dari Gudang ke Display Rak (FEFO Gudang)
    // ══════════════════════════════════════════════════════════════════

    /**
     * Pindahkan sejumlah stok dari gudang ke rak display.
     * Menggunakan algoritma FEFO: batch dengan ED terdekat dipindah lebih dulu.
     *
     * @throws Exception Jika stok gudang tidak mencukupi
     */
    public function transferKeRak(int $obatId, int $jumlah, int $userId, ?string $keterangan = null): array
    {
        return DB::transaction(function () use ($obatId, $jumlah, $userId, $keterangan) {
            $obat = Obat::findOrFail($obatId);
            $totalGudang = $obat->batches()->sum('stok_gudang');

            if ($totalGudang < $jumlah) {
                throw new Exception("Stok di gudang tidak mencukupi. Tersedia: {$totalGudang} {$obat->satuan}");
            }

            $sisa = $jumlah;
            $batchDipindahkan = [];

            // Ambil batch gudang urut ED terdekat (FEFO)
            $batches = ObatBatch::where('obat_id', $obatId)
                ->where('stok_gudang', '>', 0)
                ->orderBy('tanggal_kadaluwarsa', 'asc')
                ->lockForUpdate()
                ->get();

            foreach ($batches as $batch) {
                if ($sisa <= 0) break;

                $ambil = min($batch->stok_gudang, $sisa);
                $batch->decrement('stok_gudang', $ambil);
                $batch->increment('stok_rak', $ambil);

                TransferRak::create([
                    'obat_id'          => $obatId,
                    'obat_batch_id'    => $batch->id,
                    'user_id'          => $userId,
                    'jumlah'           => $ambil,
                    'tanggal_transfer' => now()->toDateString(),
                    'keterangan'       => $keterangan ?? 'Transfer stok ke display rak',
                ]);

                $batchDipindahkan[] = [
                    'nomor_batch' => $batch->nomor_batch,
                    'jumlah'      => $ambil,
                    'ed'          => $batch->tanggal_kadaluwarsa->format('d/m/Y'),
                ];

                $sisa -= $ambil;
            }

            return $batchDipindahkan;
        });
    }

    // ══════════════════════════════════════════════════════════════════
    // 2. Transaksi Kasir / POS (FEFO Rak) + Cek ROP Otomatis
    // ══════════════════════════════════════════════════════════════════

    /**
     * Proses checkout kasir. Mengurangi stok_rak via FEFO.
     * Setiap item yang terjual dicek terhadap ROP & min stok rak.
     *
     * @param  array $dataTransaksi  ['total_harga', 'nominal_bayar', 'kembalian', 'nama_pembeli'?, 'catatan'?]
     * @param  array $items          [['obat_id', 'jumlah'], ...]
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
                $qtyBeli = (int) $item['jumlah'];

                $stokRakTersedia = $obat->batches()->sum('stok_rak');

                if ($stokRakTersedia < $qtyBeli) {
                    throw new Exception(
                        "Stok rak obat \"{$obat->nama_obat}\" tidak mencukupi " .
                        "(Tersisa di rak: {$stokRakTersedia} {$obat->satuan}). " .
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
                        'jumlah'        => $ambil,
                        'harga_satuan'  => $obat->harga,
                        'subtotal'      => $ambil * $obat->harga,
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
    // 3. Pengecekan Otomatis ROP (Total Apotek) & Ambang Batas Rak
    // ══════════════════════════════════════════════════════════════════

    /**
     * Evaluasi dua kondisi peringatan stok:
     * A. Total Stok Apotek ≤ ROP → Kirim notifikasi WA, suggest pesanan
     * B. Stok Rak ≤ min_stok_rak → Log notifikasi restock_rak
     */
    public function cekRopDanRak(Obat $obat): void
    {
        $stokGudang  = $obat->batches()->sum('stok_gudang');
        $stokRak     = $obat->batches()->sum('stok_rak');
        $totalApotek = $stokGudang + $stokRak;

        // A. Pemicu ROP (Total Apotek ≤ ROP → Pesan ke Supplier)
        if ($obat->rop_minimum > 0 && $totalApotek <= $obat->rop_minimum) {
            $sudahAda = Notifikasi::where('obat_id', $obat->id)
                ->where('jenis_notifikasi', 'stok_menipis')
                ->whereDate('created_at', today())
                ->exists();

            if (! $sudahAda) {
                $pesan = "*PERINGATAN ROP — Stok Menipis*\n"
                    . "Obat: *{$obat->nama_obat}*\n"
                    . "Total Apotek: *{$totalApotek} {$obat->satuan}* (Gudang: {$stokGudang} | Rak: {$stokRak})\n"
                    . "Batas ROP: *{$obat->rop_minimum} {$obat->satuan}*\n"
                    . "Segera lakukan pemesanan ulang ke supplier.";

                $notif = Notifikasi::create([
                    'obat_id'          => $obat->id,
                    'jenis_notifikasi' => 'stok_menipis',
                    'pesan'            => $pesan,
                    'target_nomor'     => config('services.fonnte.admin_target', '08123456789'),
                    'status'           => 'pending',
                ]);

                KirimNotifikasiWhatsapp::dispatch($notif);
            }
        }

        // B. Peringatan Rak Kosong (stok_rak ≤ min_stok_rak)
        if ($obat->min_stok_rak > 0 && $stokRak <= $obat->min_stok_rak && $stokGudang > 0) {
            $sudahAda = Notifikasi::where('obat_id', $obat->id)
                ->where('jenis_notifikasi', 'restock_rak')
                ->whereDate('created_at', today())
                ->exists();

            if (! $sudahAda) {
                Notifikasi::create([
                    'obat_id'          => $obat->id,
                    'jenis_notifikasi' => 'restock_rak',
                    'pesan'            => "📦 Stok rak obat *{$obat->nama_obat}* menipis ({$stokRak} {$obat->satuan}). Gudang masih ada {$stokGudang} {$obat->satuan}. Silakan lakukan Transfer ke Rak.",
                    'target_nomor'     => config('services.fonnte.admin_target', '08123456789'),
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

            $sisaHari   = now()->diffInDays($batch->tanggal_kadaluwarsa);
            $stokBatch  = $batch->stok_gudang + $batch->stok_rak;

            $notif = Notifikasi::create([
                'obat_id'          => $batch->obat_id,
                'jenis_notifikasi' => 'mendekati_kadaluwarsa',
                'pesan'            => "⏰ *MENDEKATI KADALUWARSA*\n"
                    . "Obat: *{$batch->obat->nama_obat}*\n"
                    . "No. Batch: {$batch->nomor_batch}\n"
                    . "ED: {$batch->tanggal_kadaluwarsa->format('d/m/Y')} ({$sisaHari} hari lagi)\n"
                    . "Sisa Stok Batch: {$stokBatch} {$batch->obat->satuan} (Gudang: {$batch->stok_gudang} | Rak: {$batch->stok_rak})",
                'target_nomor'     => config('services.fonnte.admin_target', '08123456789'),
                'status'           => 'pending',
            ]);

            KirimNotifikasiWhatsapp::dispatch($notif);
            $count++;
        }

        return $count;
    }
}
