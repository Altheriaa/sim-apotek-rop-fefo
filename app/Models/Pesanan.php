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

    /**
     * Format nomor resmi Surat Pesanan: [No]/TF/[Bulan Romawi]/[Tahun]
     * Contoh: 02/TF/VI/2026
     */
    public function getNomorSuratAttribute(): string
    {
        $bulanRomawi = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        $tanggal = $this->tanggal_pesan ? \Carbon\Carbon::parse($this->tanggal_pesan) : now();
        $month = (int) $tanggal->format('n');
        $romawi = $bulanRomawi[$month] ?? 'I';
        $no = str_pad((string) $this->id, 2, '0', STR_PAD_LEFT);
        $year = $tanggal->format('Y');

        return "{$no}/TF/{$romawi}/{$year}";
    }
}
