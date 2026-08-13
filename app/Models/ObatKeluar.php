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
        'jumlah',
    ];

    protected $casts = [
        'tanggal_keluar' => 'date',
    ];

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
