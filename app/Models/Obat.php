<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Obat extends Model
{
    protected $table = 'obat';

    protected $fillable = [
        'nama_obat',
        'satuan',
        'rop_minimum',
    ];

    protected $appends = ['stok_total'];

    // ── Accessor: stok_total dihitung dari SUM(stok_sisa) semua batch ──

    protected function stokTotal(): Attribute
    {
        return Attribute::make(
            get: fn () => (int) $this->batches()->sum('stok_sisa'),
        );
    }

    // ── Relations ──

    public function batches(): HasMany
    {
        return $this->hasMany(ObatBatch::class);
    }

    /**
     * Batch aktif: stok_sisa > 0, urut berdasarkan tanggal kadaluwarsa ASC (FEFO)
     */
    public function activeBatches(): HasMany
    {
        return $this->batches()
            ->where('stok_sisa', '>', 0)
            ->orderBy('tanggal_kadaluwarsa', 'asc');
    }

    public function obatKeluar(): HasMany
    {
        return $this->hasMany(ObatKeluar::class);
    }

    public function notifikasi(): HasMany
    {
        return $this->hasMany(Notifikasi::class);
    }
}
