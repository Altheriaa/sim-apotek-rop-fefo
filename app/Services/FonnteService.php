<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    /**
     * Kirim pesan WhatsApp via Fonnte API.
     *
     * @param  string  $target  Nomor telepon tujuan
     * @param  string  $pesan   Isi pesan
     * @return array             Response dari Fonnte API
     */
    public function kirim(string $target, string $pesan): array
    {
        $token = config('services.fonnte.token');

        if (empty($token)) {
            Log::warning('Fonnte: Token belum dikonfigurasi.');
            return ['status' => false, 'reason' => 'Token belum dikonfigurasi'];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])
                ->asForm()
                ->post('https://api.fonnte.com/send', [
                    'target'      => $target,
                    'message'     => $pesan,
                    'countryCode' => '62',
                ]);

            $result = $response->json();

            Log::info('Fonnte: Pesan terkirim', [
                'target' => $target,
                'status' => $result['status'] ?? 'unknown',
                'id'     => $result['id'] ?? null,
            ]);

            return $result;
        } catch (\Exception $e) {
            Log::error('Fonnte: Gagal kirim pesan', [
                'target' => $target,
                'error'  => $e->getMessage(),
            ]);

            return ['status' => false, 'reason' => $e->getMessage()];
        }
    }
}
