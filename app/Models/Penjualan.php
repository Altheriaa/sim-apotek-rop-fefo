<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

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

    /**
     * Generate Nomor Transaksi unik (Format: TRX-YYYYMMDD-0001)
     */
    public static function generateNoTransaksi(?string $date = null): string
    {
        $dateStr = $date ? date('Ymd', strtotime($date)) : now()->format('Ymd');
        $prefix = "TRX-{$dateStr}-";

        $driver = DB::connection()->getDriverName();
        $query = self::where('no_transaksi', 'like', "{$prefix}%");

        if ($driver === 'mysql') {
            $query->orderByRaw("CAST(SUBSTRING_INDEX(no_transaksi, '-', -1) AS UNSIGNED) DESC");
        } else {
            $query->orderBy('no_transaksi', 'desc');
        }

        $last = $query->lockForUpdate()->first();

        if ($last && preg_match('/(\d+)$/', $last->no_transaksi, $matches)) {
            $nextNumber = ((int) $matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }

        do {
            $noTransaksi = $prefix . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
            $nextNumber++;
        } while (self::where('no_transaksi', $noTransaksi)->exists());

        return $noTransaksi;
    }
}
