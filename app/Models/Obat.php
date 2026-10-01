<?php

namespace App\Models;

use App\Models\DetailPenjualan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\DB;

class Obat extends Model
{
    protected $table = 'obat';

    protected $fillable = [
        'kode_obat',
        'supplier_id',
        'nama_obat',
        'kategori',
        'satuan_beli',       // Satuan saat beli dari supplier (Box, Dus, Botol)
        'satuan_jual',       // Satuan saat jual ke pasien (Strip, Sachet, Botol)
        'isi_per_kemasan',   // 1 satuan_beli = N satuan_jual (misal 1 Box = 10 Strip)
        'harga_jual',        // Harga jual per satuan_jual
        'rop_minimum',       // ROP statis fallback (satuan_beli)
        'lead_time_hari',    // Estimasi hari pengiriman supplier (untuk ROP dinamis)
        'min_stok_rak',      // Min stok rak dalam satuan_jual (Strip, Sachet, Botol)
    ];

    protected $appends = ['stok_total', 'stok_gudang_total', 'stok_rak_total', 'rata_rata_pemakaian_harian', 'rop_dinamis'];

    // ── Accessor: Total stok apotek dinormalisasi ke satuan_jual ──
    // Formula: (stok_gudang × isi_per_kemasan) + stok_rak
    protected function stokTotal(): Attribute
    {
        return Attribute::make(
            get: fn () => (int) (
                ($this->batches()->sum('stok_gudang') * $this->isi_per_kemasan)
                + $this->batches()->sum('stok_rak')
            ),
        );
    }

    // ── Accessor: Total stok di gudang fisik (dalam satuan_beli) ──
    protected function stokGudangTotal(): Attribute
    {
        return Attribute::make(
            get: fn () => (int) $this->batches()->sum('stok_gudang'),
        );
    }

    // ── Accessor: Total stok di display rak (dalam satuan_jual) ──
    protected function stokRakTotal(): Attribute
    {
        return Attribute::make(
            get: fn () => (int) $this->batches()->sum('stok_rak'),
        );
    }

    // ── Accessor: Rata-rata pemakaian harian (satuan_jual) — 30 hari terakhir ──
    // Dihitung dari data penjualan aktual. Jika belum ada riwayat, return 0.
    protected function rataRataPemakaianHarian(): Attribute
    {
        return Attribute::make(
            get: function () {
                $totalTerjual = DetailPenjualan::where('obat_id', $this->id)
                    ->whereHas('penjualan', fn ($q) =>
                        $q->where('tanggal_transaksi', '>=', now()->subDays(30))
                    )
                    ->sum('jumlah'); // jumlah dalam satuan_jual

                return round($totalTerjual / 30, 2);
            },
        );
    }

    // ── Accessor: ROP Dinamis = D × L (satuan_jual) ──
    // D = rata_rata_pemakaian_harian, L = lead_time_hari
    // Dinormalisasi ke satuan_beli (dibulatkan ke atas) agar kompatibel dengan sistem pesanan.
    // Fallback ke rop_minimum jika belum ada riwayat pemakaian.
    protected function ropDinamis(): Attribute
    {
        return Attribute::make(
            get: function () {
                $d = $this->rata_rata_pemakaian_harian; // satuan_jual/hari
                $l = (int) ($this->lead_time_hari ?? 3);

                if ($d <= 0) {
                    // Belum ada riwayat penjualan — pakai ROP statis
                    return (int) $this->rop_minimum;
                }

                // ROP dalam satuan_jual
                $ropSatuanJual = $d * $l;

                // Konversi ke satuan_beli (ceil agar tidak pernah pesan kurang)
                $isiPerKemasan = max((int) $this->isi_per_kemasan, 1);
                return (int) ceil($ropSatuanJual / $isiPerKemasan);
            },
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

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
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
