<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel pencatatan pembuangan/disposal batch obat yang expired atau rusak.
     * Obat hanya keluar dari sistem melalui:
     *   1. Transaksi kasir (detail_penjualan)
     *   2. Disposal batch (tabel ini) — untuk expired/rusak
     */
    public function up(): void
    {
        Schema::create('obat_keluar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('obat_id')->constrained('obat');
            $table->foreignId('obat_batch_id')->constrained('obat_batch');
            $table->foreignId('user_id')->constrained('users');
            $table->date('tanggal_keluar');
            $table->integer('jumlah_gudang')->default(0)->comment('Jumlah dibuang dari stok_gudang (satuan_beli)');
            $table->integer('jumlah_rak')->default(0)->comment('Jumlah dibuang dari stok_rak (satuan_jual)');
            $table->enum('alasan', ['expired', 'rusak', 'lainnya'])->default('expired');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('obat_keluar');
    }
};
