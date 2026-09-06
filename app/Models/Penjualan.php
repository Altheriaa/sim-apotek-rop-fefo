<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penjualan extends Model
{
    protected $table = 'penjualan';

    protected $fillable = [
        'no_transaksi',
        'user_id',
        'tanggal_transaksi',
        'total_harga',
        'nominal_bayar',
        'kembalian',
        'nama_pembeli',
        'catatan',
    ];

    protected $casts = [
        'tanggal_transaksi' => 'datetime',
    ];

    // ── Relations ──

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(DetailPenjualan::class);
    }

    // ── Accessors Laba / HPP ──

    /**
     * Total Modal (HPP) seluruh item dalam transaksi
     */
    public function getTotalHppAttribute(): float
    {
        return (float) $this->details->sum(fn ($detail) => $detail->total_hpp);
    }

    /**
     * Total Laba / Keuntungan Bersih transaksi
     */
    public function getTotalLabaAttribute(): float
    {
        return (float) $this->total_harga - $this->total_hpp;
    }
}
