<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pesanan extends Model
{
    protected $table = 'pesanan';

    protected $fillable = [
        'kode_pesanan',
        'supplier_id',
        'user_id',
        'tanggal_pesan',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal_pesan' => 'date',
    ];

    // ── Relations ──

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function detailPesanan(): HasMany
    {
        return $this->hasMany(DetailPesanan::class);
    }

    // ── Helpers ──

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isSelesai(): bool
    {
        return $this->status === 'selesai';
    }
}
