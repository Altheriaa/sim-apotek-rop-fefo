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

    // ── Helpers ──

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isTerkirim(): bool
    {
        return $this->status === 'terkirim';
    }
}
