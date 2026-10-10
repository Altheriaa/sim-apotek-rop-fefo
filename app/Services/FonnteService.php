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

        if (trim($target) === '') {
            Log::warning('Fonnte: Nomor tujuan kosong (cek FONNTE_ADMIN_TARGET).');
            return ['status' => false, 'reason' => 'Nomor tujuan kosong'];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => trim($token),
            ])
                ->asForm()
                ->post('https://api.fonnte.com/send', [
                    'target'      => $target,
                    'message'     => $pesan,
                    'countryCode' => '62',
                ]);

            $result = $response->json();

            if (! is_array($result)) {
                Log::error('Fonnte: Respons tidak valid', [
                    'target' => $target,
                    'http'   => $response->status(),
                    'body'   => mb_substr($response->body(), 0, 300),
                ]);

                return ['status' => false, 'reason' => 'Respons tidak valid (HTTP ' . $response->status() . ')'];
            }

            if (($result['status'] ?? false) === true) {
                Log::info('Fonnte: Pesan terkirim', [
                    'target' => $target,
                    'id'     => $result['id'] ?? null,
                ]);
            } else {
                Log::warning('Fonnte: Pesan ditolak', [
                    'target' => $target,
                    'reason' => $result['reason'] ?? 'tidak diketahui',
                ]);
            }

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
