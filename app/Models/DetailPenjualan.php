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

    // ── Accessors Laba / HPP ──

    /**
     * HPP / Modal beli per satuan_jual (misal per Strip)
     */
    public function getHppSatuanAttribute(): float
    {
        if (! $this->obatBatch) {
            return 0;
        }

        $isiPerKemasan = max(1, (int) ($this->obat->isi_per_kemasan ?? 1));
        $hargaBeliBox  = (float) ($this->obatBatch->harga_beli_satuan ?? 0);

        return $hargaBeliBox / $isiPerKemasan;
    }

    /**
     * Total Modal (HPP) untuk baris item ini
     */
    public function getTotalHppAttribute(): float
    {
        return $this->hpp_satuan * (int) $this->jumlah;
    }

    /**
     * Laba / Keuntungan Bersih untuk baris item ini
     */
    public function getLabaAttribute(): float
    {
        return (float) $this->subtotal - $this->total_hpp;
    }
}
