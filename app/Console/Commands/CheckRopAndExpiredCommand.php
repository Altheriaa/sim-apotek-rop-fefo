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

        // 1. Cek ROP untuk semua obat
        $obatList = Obat::where('rop_minimum', '>', 0)->get();
        $ropCount = 0;

        foreach ($obatList as $obat) {
            if ($obat->stok_total <= $obat->rop_minimum) {
                $stokService->cekRop($obat);
                $ropCount++;
            }
        }

        $this->info("ROP: {$ropCount} obat di bawah/sama dengan ROP minimum.");

        // 2. Cek kadaluwarsa (30 hari ke depan)
        $edCount = $stokService->cekKadaluwarsa(30);
        $this->info("Kadaluwarsa: {$edCount} notifikasi baru dibuat.");

        $this->info('Pengecekan selesai.');

        return Command::SUCCESS;
    }
}
