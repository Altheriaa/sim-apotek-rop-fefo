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
        'stok_gudang',   // Stok di gudang fisik
        'stok_rak',      // Stok di display rak
        'harga_beli',
    ];

    protected $casts = [
        'tanggal_masuk'      => 'date',
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

    public function transferRak(): HasMany
    {
        return $this->hasMany(TransferRak::class);
    }

    public function detailPenjualan(): HasMany
    {
        return $this->hasMany(DetailPenjualan::class);
    }

    // ── Helpers ──

    /**
     * Stok total batch ini (gudang + rak)
     */
    public function stokSisa(): int
    {
        return $this->stok_gudang + $this->stok_rak;
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->tanggal_kadaluwarsa->lte(now()->addDays($days));
    }

    public function isExpired(): bool
    {
        return $this->tanggal_kadaluwarsa->lt(now());
    }

    /**
     * Ekstrak 3 huruf singkatan dari nama obat
     */
    public static function extractPrefix(?Obat $obat): string
    {
        if (!$obat) return 'BTC';

        $name = strtoupper(trim(preg_replace('/[^a-zA-Z]/', '', $obat->nama_obat)));
        if (strlen($name) >= 3) {
            $consonants = preg_replace('/[AEIOU]/', '', $name);
            if (strlen($consonants) >= 3) {
                return substr($consonants, 0, 3);
            }
            return substr($name, 0, 3);
        }
        return str_pad($name ?: 'BTC', 3, 'X');
    }

    /**
     * Generate Nomor Batch otomatis (Format: PCT-2026-001, BTC-2026-001, dst)
     */
    public static function generateNomorBatch(?int $obatId = null): string
    {
        $year = date('Y');
        $prefix = 'BTC';

        if ($obatId) {
            $obat = Obat::find($obatId);
            $prefix = self::extractPrefix($obat);
        }

        $lastBatch = self::where('nomor_batch', 'like', "{$prefix}-{$year}-%")
            ->orderByRaw("CAST(SUBSTRING_INDEX(nomor_batch, '-', -1) AS UNSIGNED) DESC")
            ->first();

        if ($lastBatch && preg_match("/" . preg_quote($prefix, '/') . "-{$year}-(\\d+)/", $lastBatch->nomor_batch, $matches)) {
            $nextNumber = ((int) $matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }

        do {
            $nomorBatch = "{$prefix}-{$year}-" . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            $nextNumber++;
        } while (self::where('nomor_batch', $nomorBatch)->exists());

        return $nomorBatch;
    }
}
