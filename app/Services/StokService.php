<?php

namespace App\Services;

use App\Models\Obat;
use App\Models\ObatBatch;
use App\Models\ObatKeluar;
use App\Models\Notifikasi;
use App\Jobs\KirimNotifikasiWhatsapp;
use Illuminate\Support\Facades\DB;

class StokService
{
    /**
     * Keluarkan stok obat menggunakan metode FEFO (First Expired First Out).
     * Batch dengan tanggal kadaluwarsa terdekat akan dikurangi lebih dulu.
     *
     * @param  int  $obatId   ID obat yang akan dikeluarkan
     * @param  int  $jumlah   Jumlah yang akan dikeluarkan
     * @param  int  $userId   ID user yang melakukan transaksi
     * @return array           Daftar batch yang terpakai beserta jumlahnya
     *
     * @throws \Exception Jika stok tidak mencukupi
     */
    public function keluarkanObat(int $obatId, int $jumlah, int $userId): array
    {
        $obat = Obat::findOrFail($obatId);

        if ($obat->stok_total < $jumlah) {
            throw new \Exception('Stok tidak mencukupi. Tersisa: ' . $obat->stok_total . ' ' . $obat->satuan);
        }

        $sisa = $jumlah;
        $batchTerpakai = [];

        DB::transaction(function () use ($obat, &$sisa, $userId, &$batchTerpakai) {
            // Ambil batch aktif urut berdasarkan tanggal kadaluwarsa ASC (FEFO)
            $batches = ObatBatch::where('obat_id', $obat->id)
                ->where('stok_sisa', '>', 0)
                ->orderBy('tanggal_kadaluwarsa', 'asc')
                ->lockForUpdate()
                ->get();

            foreach ($batches as $batch) {
                if ($sisa <= 0) break;

                $ambil = min($batch->stok_sisa, $sisa);
                $batch->decrement('stok_sisa', $ambil);

                ObatKeluar::create([
                    'obat_id'       => $obat->id,
                    'obat_batch_id' => $batch->id,
                    'user_id'       => $userId,
                    'tanggal_keluar' => now()->toDateString(),
                    'jumlah'        => $ambil,
                ]);

                $batchTerpakai[] = [
                    'batch_id'  => $batch->id,
                    'no_batch'  => $batch->no_batch,
                    'jumlah'    => $ambil,
                    'ed'        => $batch->tanggal_kadaluwarsa->format('d/m/Y'),
                ];

                $sisa -= $ambil;
            }
        });

        // Cek ROP setelah transaksi
        $this->cekRop($obat->fresh());

        return $batchTerpakai;
    }

    /**
     * Cek apakah stok obat sudah mencapai/melewati Reorder Point (ROP).
     * Jika ya, buat notifikasi dan dispatch job kirim WA.
     */
    public function cekRop(Obat $obat): void
    {
        if ($obat->rop_minimum <= 0) return;

        if ($obat->stok_total <= $obat->rop_minimum) {
            // Cek apakah sudah ada notifikasi pending hari ini untuk obat ini
            $sudahAda = Notifikasi::where('obat_id', $obat->id)
                ->where('jenis_notifikasi', 'stok_menipis')
                ->whereDate('created_at', today())
                ->exists();

            if ($sudahAda) return;

            $notif = Notifikasi::create([
                'obat_id'           => $obat->id,
                'jenis_notifikasi'  => 'stok_menipis',
                'pesan'             => "⚠️ STOK MENIPIS\n\nObat: {$obat->nama_obat}\nStok tersisa: {$obat->stok_total} {$obat->satuan}\nROP: {$obat->rop_minimum} {$obat->satuan}\n\nSegera lakukan pemesanan ulang.",
                'target_nomor'      => config('services.fonnte.admin_target'),
            ]);

            KirimNotifikasiWhatsapp::dispatch($notif);
        }
    }

    /**
     * Cek batch obat yang mendekati kadaluwarsa (≤ 30 hari).
     * Dipanggil oleh scheduled command harian.
     */
    public function cekKadaluwarsa(int $days = 30): int
    {
        $batches = ObatBatch::with('obat')
            ->where('stok_sisa', '>', 0)
            ->whereBetween('tanggal_kadaluwarsa', [now(), now()->addDays($days)])
            ->get();

        $count = 0;

        foreach ($batches as $batch) {
            // Cek apakah sudah ada notifikasi hari ini untuk batch ini
            $sudahAda = Notifikasi::where('obat_id', $batch->obat_id)
                ->where('jenis_notifikasi', 'mendekati_kadaluwarsa')
                ->whereDate('created_at', today())
                ->exists();

            if ($sudahAda) continue;

            $sisaHari = now()->diffInDays($batch->tanggal_kadaluwarsa);

            $notif = Notifikasi::create([
                'obat_id'           => $batch->obat_id,
                'jenis_notifikasi'  => 'mendekati_kadaluwarsa',
                'pesan'             => "⏰ MENDEKATI KADALUWARSA\n\nObat: {$batch->obat->nama_obat}\nNo Batch: {$batch->no_batch}\nED: {$batch->tanggal_kadaluwarsa->format('d/m/Y')}\nSisa: {$sisaHari} hari lagi\nStok batch: {$batch->stok_sisa} {$batch->obat->satuan}",
                'target_nomor'      => config('services.fonnte.admin_target'),
            ]);

            KirimNotifikasiWhatsapp::dispatch($notif);
            $count++;
        }

        return $count;
    }
}
