<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Obat extends Model
{
    protected $table = 'obat';

    protected $fillable = [
        'kode_obat',
        'nama_obat',
        'kategori',
        'satuan',
        'harga',
        'rop_minimum',
        'min_stok_rak',
    ];

    protected $appends = ['stok_total', 'stok_gudang_total', 'stok_rak_total'];

    // ── Accessor: Total stok keseluruhan apotek (gudang + rak) ──
    protected function stokTotal(): Attribute
    {
        return Attribute::make(
            get: fn () => (int) ($this->batches()->sum('stok_gudang') + $this->batches()->sum('stok_rak')),
        );
    }

    // ── Accessor: Total stok di gudang fisik ──
    protected function stokGudangTotal(): Attribute
    {
        return Attribute::make(
            get: fn () => (int) $this->batches()->sum('stok_gudang'),
        );
    }

    // ── Accessor: Total stok di display rak ──
    protected function stokRakTotal(): Attribute
    {
        return Attribute::make(
            get: fn () => (int) $this->batches()->sum('stok_rak'),
        );
    }

    // ── Relations ──

    public function batches(): HasMany
    {
        return $this->hasMany(ObatBatch::class);
    }

    /**
     * Batch dengan stok gudang > 0, urut FEFO (ED terdekat dulu)
     */
    public function batchesGudang(): HasMany
    {
        return $this->batches()
            ->where('stok_gudang', '>', 0)
            ->orderBy('tanggal_kadaluwarsa', 'asc');
    }

    /**
     * Batch dengan stok rak > 0, urut FEFO (ED terdekat dulu)
     */
    public function batchesRak(): HasMany
    {
        return $this->batches()
            ->where('stok_rak', '>', 0)
            ->orderBy('tanggal_kadaluwarsa', 'asc');
    }

    public function transferRak(): HasMany
    {
        return $this->hasMany(TransferRak::class);
    }

    public function penjualanDetails(): HasMany
    {
        return $this->hasMany(DetailPenjualan::class);
    }

    public function notifikasi(): HasMany
    {
        return $this->hasMany(Notifikasi::class);
    }

    public function pesananDetails(): HasMany
    {
        return $this->hasMany(DetailPesanan::class);
    }

    /**
     * Generate Kode Obat otomatis (Format: OBT-001, OBT-002, dst)
     */
    public static function generateKodeObat(): string
    {
        $lastObat = self::where('kode_obat', 'like', 'OBT-%')
            ->orderByRaw('CAST(SUBSTRING(kode_obat, 5) AS UNSIGNED) DESC')
            ->first();

        if ($lastObat && preg_match('/OBT-(\d+)/', $lastObat->kode_obat, $matches)) {
            $nextNumber = ((int) $matches[1]) + 1;
        } else {
            $nextNumber = self::count() + 1;
        }

        do {
            $kode = 'OBT-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            $nextNumber++;
        } while (self::where('kode_obat', $kode)->exists());

        return $kode;
    }
}
