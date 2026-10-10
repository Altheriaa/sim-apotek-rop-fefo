<?php

namespace App\Jobs;

use App\Models\Notifikasi;
use App\Services\FonnteService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class KirimNotifikasiWhatsapp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    /** Jeda antar percobaan ulang (detik). */
    public array $backoff = [30, 120];

    public function __construct(
        public Notifikasi $notifikasi
    ) {}

    public function handle(FonnteService $fonnteService): void
    {
        $result = $fonnteService->kirim(
            $this->notifikasi->target_nomor,
            $this->notifikasi->pesan
        );

        if (($result['status'] ?? false) !== true) {
            // Lempar exception supaya antrean mencoba ulang; setelah percobaan
            // terakhir failed() menandai notifikasi sebagai gagal.
            throw new \RuntimeException('Fonnte menolak pesan: ' . ($result['reason'] ?? 'tidak diketahui'));
        }

        $this->notifikasi->update([
            'status'     => 'terkirim',
            'fonnte_id'  => is_array($result['id'] ?? null) ? ($result['id'][0] ?? null) : ($result['id'] ?? null),
            'dikirim_at' => now(),
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        $this->notifikasi->update([
            'status' => 'gagal',
        ]);
    }
}
