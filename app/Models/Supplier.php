<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $table = 'supplier';

    protected $fillable = [
        'nama_supplier',
        'alamat',
        'kontak',
    ];

    // ── Relations ──

    public function obatBatch(): HasMany
    {
        return $this->hasMany(ObatBatch::class);
    }

    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class);
    }
}
