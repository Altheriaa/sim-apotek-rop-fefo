<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransferRak extends Model
{
    protected $table = 'transfer_rak';

    protected $fillable = [
        'obat_id',
        'obat_batch_id',
        'user_id',
        'jumlah',
        'tanggal_transfer',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_transfer' => 'date',
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
