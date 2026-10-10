<?php

namespace App\Services;

use App\Models\Obat;
use App\Models\ObatBatch;
use App\Models\TransferRak;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\Notifikasi;
use App\Models\ObatKeluar;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Jobs\KirimNotifikasiWhatsapp;
use Illuminate\Support\Facades\DB;
use Exception;

class StokService
{
    /** Jeda antar pesan WA dalam satu proses (detik), agar tidak diblokir Fonnte. */
    private const JEDA_KIRIM_DETIK = 3;

    private int $urutanKirim = 0;

    public function hitungTotalStokSatuanJual(Obat $obat): int
    {
        $stokGudang = $obat->batches()->sum('stok_gudang');
        $stokRak    = $obat->batches()->sum('stok_rak');
        return ($stokGudang * $obat->isi_per_kemasan) + $stokRak;
    }

    /**
     * @throws Exception
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

                $batch->decrement('stok_gudang', $ambilBox);
                $batch->increment('stok_rak', $hasilSatuanJual);

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

    /**
     * @param  array $dataTransaksi 
     * @param  array $items          
     * @throws Exception 
     */
    public function prosesPenjualan(array $dataTransaksi, array $items, int $userId): Penjualan
    {
        return DB::transaction(function () use ($dataTransaksi, $items, $userId) {
            $noTransaksi = Penjualan::generateNoTransaksi();

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
                $qtyBeli = (int) $item['jumlah']; // satuan_jual

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
                        'jumlah'        => $ambil,
                        'harga_satuan'  => $obat->harga_jual,
                        'subtotal'      => $ambil * $obat->harga_jual,
                    ]);

                    $sisa -= $ambil;
                }

                // Evaluasi ROP & status rak setelah setiap item terjual
                $this->cekRopDanRak($obat->fresh(), $userId);
            }

            return $penjualan;
        });
    }

    /**
     * @param  int $userId  
     */
    public function cekRopDanRak(Obat $obat, int $userId = 0): int
    {
        $dibuat = 0;
        $stokGudang         = $obat->batches()->sum('stok_gudang');
        $stokRak            = $obat->batches()->sum('stok_rak');
        $totalSatuanJual    = ($stokGudang * $obat->isi_per_kemasan) + $stokRak;
        $ropDinamis         = $obat->rop_dinamis;
        $batasRopSatuanJual = $ropDinamis * $obat->isi_per_kemasan;

        if ($ropDinamis > 0 && $totalSatuanJual <= $batasRopSatuanJual) {
            $sudahAda = Notifikasi::where('obat_id', $obat->id)
                ->where('jenis_notifikasi', 'stok_menipis')
                ->whereDate('created_at', today())
                ->exists();

            if (! $sudahAda) {
                $gudangDlmJual = $stokGudang * $obat->isi_per_kemasan;

                $obat->loadMissing('supplier');
                $supplierInfo = '';
                if ($obat->supplier) {
                    $supplierInfo = "\nSupplier: *{$obat->supplier->nama_supplier}*";
                    if ($obat->supplier->kontak) {
                        $supplierInfo .= " ({$obat->supplier->kontak})";
                    }
                }

                $pesan = "*PERINGATAN ROP — Stok Menipis*\n"
                    . "Obat: *{$obat->nama_obat}*\n"
                    . "Total Apotek: *{$totalSatuanJual} {$obat->satuan_jual}*\n"
                    . "  → Gudang: {$stokGudang} {$obat->satuan_beli} (= {$gudangDlmJual} {$obat->satuan_jual})\n"
                    . "  → Rak: {$stokRak} {$obat->satuan_jual}\n"
                    . "Batas ROP Dinamis: *{$ropDinamis} {$obat->satuan_beli}*" . ($obat->isi_per_kemasan > 1 ? " (= {$batasRopSatuanJual} {$obat->satuan_jual})" : "") . "\n"
                    . "Lead Time: {$obat->lead_time_hari} hari | Rata-rata pakai: {$obat->rata_rata_pemakaian_harian} {$obat->satuan_jual}/hari\n"
                    . $supplierInfo
                    . "\nSegera lakukan pemesanan ulang ke supplier.";

                $notif = Notifikasi::create([
                    'obat_id'          => $obat->id,
                    'jenis_notifikasi' => 'stok_menipis',
                    'pesan'            => $pesan,
                    'target_nomor'     => $this->targetAdmin(),
                    'status'           => 'pending',
                ]);

                $this->kirimWa($notif);
                $dibuat++;
                $this->generateDraftPesananRop($obat, $userId);
            }
        }

        // B. Peringatan rak kosong: stok_rak sudah di bawah min_stok_rak tapi gudang masih ada
        if ($obat->min_stok_rak > 0 && $stokRak <= $obat->min_stok_rak && $stokGudang > 0) {
            $sudahAda = Notifikasi::where('obat_id', $obat->id)
                ->where('jenis_notifikasi', 'restock_rak')
                ->whereDate('created_at', today())
                ->exists();

            if (! $sudahAda) {
                $notif = Notifikasi::create([
                    'obat_id'          => $obat->id,
                    'jenis_notifikasi' => 'restock_rak',
                    'pesan'            => "Stok rak obat *{$obat->nama_obat}* menipis ({$stokRak} {$obat->satuan_jual}). Gudang masih ada {$stokGudang} {$obat->satuan_beli}. Silakan lakukan Transfer ke Rak.",
                    'target_nomor'     => $this->targetAdmin(),
                    'status'           => 'pending',
                ]);

                $this->kirimWa($notif);
                $dibuat++;
            }
        }

        return $dibuat;
    }

    private function targetAdmin(): string
    {
        return (string) config('services.fonnte.admin_target');
    }

    private function kirimWa(Notifikasi $notif): void
    {
        KirimNotifikasiWhatsapp::dispatch($notif)
            ->delay(now()->addSeconds($this->urutanKirim * self::JEDA_KIRIM_DETIK));

        $this->urutanKirim++;
    }

    /**
     * @param  int $userId  
     */
    public function generateDraftPesananRop(Obat $obat, int $userId = 0): ?Pesanan
    {
        if (! $obat->supplier_id) {
            return null;
        }

        $sedangDipesan = DetailPesanan::where('obat_id', $obat->id)
            ->whereHas('pesanan', function ($q) {
                $q->whereIn('status', ['draft', 'diproses', 'dikirim']);
            })
            ->exists();

        if ($sedangDipesan) {
            return null;
        }

        $pesananDraft = DB::transaction(function () use ($obat, $userId) {
            $draft = Pesanan::where('supplier_id', $obat->supplier_id)
                ->where('status', 'draft')
                ->latest('id')
                ->lockForUpdate()
                ->first();

            if (! $draft) {
                $kodePesanan = Pesanan::generateKodePesanan();

                $draft = Pesanan::create([
                    'kode_pesanan'  => $kodePesanan,
                    'supplier_id'   => $obat->supplier_id,
                    'user_id'       => $userId ?: null,
                    'tanggal_pesan' => today(),
                    'status'        => 'draft',
                    'catatan'       => 'Digenerate otomatis oleh sistem ROP (Stok Menipis)',
                ]);
            }

            return $draft;
        });

        $lastBatch     = $obat->batches()->latest('id')->first();
        $estimasiHarga = $lastBatch ? $lastBatch->harga_beli_satuan : 0;
        $jumlahPesan   = max((int) $obat->rop_dinamis * 2, 1);

        DetailPesanan::create([
            'pesanan_id'     => $pesananDraft->id,
            'obat_id'        => $obat->id,
            'jumlah_pesan'   => $jumlahPesan,
            'estimasi_harga' => $estimasiHarga,
        ]);

        return $pesananDraft;
    }

    public function cekKadaluwarsa(int $months = 6): int
    {
        $batches = ObatBatch::with('obat')
            ->where(function ($q) {
                $q->where('stok_gudang', '>', 0)->orWhere('stok_rak', '>', 0);
            })
            ->whereBetween('tanggal_kadaluwarsa', [now()->toDateString(), now()->addMonths($months)->toDateString()])
            ->get();

        $count = 0;

        foreach ($batches as $batch) {
            $sudahAda = Notifikasi::where('obat_id', $batch->obat_id)
                ->where('jenis_notifikasi', 'mendekati_kadaluwarsa')
                ->where('pesan', 'like', "%No. Batch: {$batch->nomor_batch}\n%")
                ->whereDate('created_at', today())
                ->exists();

            if ($sudahAda) continue;

            $sisaHari = (int) today()->diffInDays($batch->tanggal_kadaluwarsa);

            $notif = Notifikasi::create([
                'obat_id'          => $batch->obat_id,
                'jenis_notifikasi' => 'mendekati_kadaluwarsa',
                'pesan'            => "*MENDEKATI KADALUWARSA*\n"
                    . "Obat: *{$batch->obat->nama_obat}*\n"
                    . "No. Batch: {$batch->nomor_batch}\n"
                    . "ED: {$batch->tanggal_kadaluwarsa->format('d/m/Y')} ({$sisaHari} hari lagi)\n"
                    . "Sisa Stok Batch: Gudang {$batch->stok_gudang} {$batch->obat->satuan_beli} | Rak {$batch->stok_rak} {$batch->obat->satuan_jual}",
                'target_nomor'     => $this->targetAdmin(),
                'status'           => 'pending',
            ]);

            $this->kirimWa($notif);
            $count++;
        }

        return $count;
    }

    /**
     * @param  int    $batchId       
     * @param  int    $jumlahGudang  
     * @param  int    $jumlahRak    
     * @param  string $alasan       
     * @param  int    $userId       
     * @param  string|null $catatan  
     * @throws Exception 
     */
    public function disposalBatch(
        int $batchId,
        int $jumlahGudang,
        int $jumlahRak,
        string $alasan,
        int $userId,
        ?string $catatan = null
    ): ObatKeluar {
        return DB::transaction(function () use ($batchId, $jumlahGudang, $jumlahRak, $alasan, $userId, $catatan) {
            $batch = ObatBatch::lockForUpdate()->findOrFail($batchId);

            if ($jumlahGudang > $batch->stok_gudang) {
                throw new Exception(
                    "Jumlah buang gudang ({$jumlahGudang}) melebihi stok gudang tersedia ({$batch->stok_gudang})."
                );
            }

            if ($jumlahRak > $batch->stok_rak) {
                throw new Exception(
                    "Jumlah buang rak ({$jumlahRak}) melebihi stok rak tersedia ({$batch->stok_rak})."
                );
            }

            if ($jumlahGudang === 0 && $jumlahRak === 0) {
                throw new Exception('Jumlah yang dibuang tidak boleh 0.');
            }

            if ($jumlahGudang > 0) {
                $batch->decrement('stok_gudang', $jumlahGudang);
            }
            if ($jumlahRak > 0) {
                $batch->decrement('stok_rak', $jumlahRak);
            }

            return ObatKeluar::create([
                'obat_id'        => $batch->obat_id,
                'obat_batch_id'  => $batchId,
                'user_id'        => $userId,
                'tanggal_keluar' => today()->toDateString(),
                'jumlah_gudang'  => $jumlahGudang,
                'jumlah_rak'     => $jumlahRak,
                'alasan'         => $alasan,
                'catatan'        => $catatan,
            ]);
        });
    }
}
