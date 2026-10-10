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

        // ROP dinamis + restock rak dicek sekaligus, satu kali per obat.
        // cekRopDanRak() sudah memvalidasi kedua kondisi dan mencegah duplikat harian.
        $notifBaru = 0;
        $perluTindakan = 0;

        foreach (Obat::with('supplier')->get() as $obat) {
            $dibuat = $stokService->cekRopDanRak($obat);
            $notifBaru += $dibuat;
            if ($dibuat > 0) {
                $perluTindakan++;
            }
        }

        $this->info("ROP/Restock Rak: {$notifBaru} notifikasi baru dibuat untuk {$perluTindakan} obat.");

        // Cek kadaluwarsa (6 bulan ke depan)
        $edCount = $stokService->cekKadaluwarsa(6);
        $this->info("Mendekati ED: {$edCount} notifikasi baru dibuat.");

        $this->info('Pengecekan selesai.');

        return Command::SUCCESS;
    }
}
