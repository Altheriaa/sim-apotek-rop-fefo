<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPenjualan extends Model
{
    protected $table = 'detail_penjualan';

    protected $fillable = [
        'penjualan_id',
        'obat_id',
        'obat_batch_id',
        'jumlah',
        'harga_satuan',
        'subtotal',
    ];

    // ── Relations ──

    public function penjualan(): BelongsTo
    {
        return $this->belongsTo(Penjualan::class);
    }

    public function obat(): BelongsTo
    {
        return $this->belongsTo(Obat::class);
    }

    public function obatBatch(): BelongsTo
    {
        return $this->belongsTo(ObatBatch::class);
    }
}
