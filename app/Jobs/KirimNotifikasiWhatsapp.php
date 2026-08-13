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

    public function __construct(
        public Notifikasi $notifikasi
    ) {}

    public function handle(FonnteService $fonnteService): void
    {
        $result = $fonnteService->kirim(
            $this->notifikasi->target_nomor,
            $this->notifikasi->pesan
        );

        if (isset($result['status']) && $result['status'] === true) {
            $this->notifikasi->update([
                'status'     => 'terkirim',
                'fonnte_id'  => $result['id'] ?? null,
                'dikirim_at' => now(),
            ]);
        } else {
            $this->notifikasi->update([
                'status' => 'gagal',
            ]);
        }
    }

    public function failed(\Throwable $exception): void
    {
        $this->notifikasi->update([
            'status' => 'gagal',
        ]);
    }
}
