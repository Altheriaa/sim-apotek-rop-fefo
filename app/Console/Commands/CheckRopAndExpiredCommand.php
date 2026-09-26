<?php

namespace App\Console\Commands;

use App\Models\Obat;
use App\Services\StokService;
use Illuminate\Console\Command;

class CheckRopAndExpiredCommand extends Command
{
    protected $signature = 'stok:check-rop-expired';

    protected $description = 'Cek ROP dan obat mendekati kadaluwarsa, kirim notifikasi WA jika perlu';

    public function handle(StokService $stokService): int
    {
        $this->info('Memulai pengecekan ROP dan kadaluwarsa...');

        // Cek ROP untuk semua obat
        $obatList = Obat::where('rop_minimum', '>', 0)->get();
        $ropCount = 0;

        foreach ($obatList as $obat) {
            if ($obat->stok_total <= ($obat->rop_minimum * $obat->isi_per_kemasan)) {
                $stokService->cekRopDanRak($obat);
                $ropCount++;
            }
        }

        $this->info("ROP: {$ropCount} obat di bawah/sama dengan ROP minimum.");

        // Cek kadaluwarsa (6 bulan ke depan)
        $edCount = $stokService->cekKadaluwarsa(6);
        $this->info("Mendekati ED: {$edCount} notifikasi baru dibuat.");

        // restock rak
        $obatList = Obat::where('min_stok_rak', '>', 0)->get();
        $restockRakCount = 0;
        foreach ($obatList as $obat) {
            if ($obat->stok_rak <= $obat->min_stok_rak) {
                $stokService->cekRopDanRak($obat);
                $restockRakCount++;
            }
        }
        $this->info("Perlu Restock Rak: {$restockRakCount} notifikasi baru dibuat.");

        $this->info('Pengecekan selesai.');

        return Command::SUCCESS;
    }
}
