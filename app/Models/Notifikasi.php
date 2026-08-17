<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notifikasi extends Model
{
    protected $table = 'notifikasi';

    protected $fillable = [
        'obat_id',
        'jenis_notifikasi',
        'pesan',
        'target_nomor',
        'status',
        'fonnte_id',
        'dikirim_at',
    ];

    protected $casts = [
        'dikirim_at' => 'datetime',
    ];

    // ── Relations ──

    public function obat(): BelongsTo
    {
        return $this->belongsTo(Obat::class);
    }

    // ── Helpers & UI Formatters ──

    public function getJudulAttribute(): string
    {
        return match ($this->jenis_notifikasi) {
            'stok_menipis'          => 'Peringatan ROP (Stok Menipis)',
            'restock_rak'           => 'Restock Rak Dibutuhkan',
            'mendekati_kadaluwarsa' => 'Mendekati Kadaluwarsa',
            'kadaluwarsa'           => 'Obat Kadaluwarsa',
            default                 => 'Pemberitahuan Stok',
        };
    }

    public function getPesanRapiAttribute(): string
    {
        // Hilangkan judul baris pertama jika duplikat
        $clean = preg_replace('/^\*?[^\n]+\*\n+/u', '', $this->pesan);
        // Bersihkan tanda format WhatsApp
        $clean = str_replace(['*'], '', $clean);
        return trim($clean);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isTerkirim(): bool
    {
        return $this->status === 'terkirim';
    }
}
