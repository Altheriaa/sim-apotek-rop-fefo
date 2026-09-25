<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ObatKeluar extends Model
{
    protected $table = 'obat_keluar';

    protected $fillable = [
        'obat_id',
        'obat_batch_id',
        'user_id',
        'tanggal_keluar',
        'jumlah_gudang',  // Jumlah dibuang dari stok_gudang (satuan_beli)
        'jumlah_rak',     // Jumlah dibuang dari stok_rak (satuan_jual)
        'alasan',         // expired | rusak | lainnya
        'catatan',
    ];

    protected $casts = [
        'tanggal_keluar' => 'date',
    ];

    // ── Label alasan disposal ──
    public static array $alasanOptions = [
        'expired' => 'Kadaluwarsa (Expired)',
        'rusak'   => 'Rusak / Tidak Layak Pakai',
        'lainnya' => 'Lainnya',
    ];

    public function getAlasanLabelAttribute(): string
    {
        return self::$alasanOptions[$this->alasan] ?? $this->alasan;
    }

    // ── Relations ──

    public function obat(): BelongsTo
    {
        return $this->belongsTo(Obat::class);
    }

    public function obatBatch(): BelongsTo
    {
        return $this->belongsTo(ObatBatch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
