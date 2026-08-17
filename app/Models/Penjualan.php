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
}
