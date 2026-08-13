<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ObatBatch extends Model
{
    protected $table = 'obat_batch';

    protected $fillable = [
        'obat_id',
        'supplier_id',
        'nomor_batch',
        'tanggal_masuk',
        'tanggal_kadaluwarsa',
        'stok_awal',
        'stok_sisa',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'tanggal_kadaluwarsa' => 'date',
    ];

    // ── Relations ──

    public function obat(): BelongsTo
    {
        return $this->belongsTo(Obat::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function obatKeluar(): HasMany
    {
        return $this->hasMany(ObatKeluar::class);
    }

    // ── Helpers ──

    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->tanggal_kadaluwarsa->lte(now()->addDays($days));
    }

    public function isExpired(): bool
    {
        return $this->tanggal_kadaluwarsa->lt(now());
    }
}
