<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel riwayat transfer stok dari Gudang ke Display Rak (FEFO Gudang)
        Schema::create('transfer_rak', function (Blueprint $table) {
            $table->id();
            $table->foreignId('obat_id')->constrained('obat');
            $table->foreignId('obat_batch_id')->constrained('obat_batch');
            $table->foreignId('user_id')->constrained('users');
            $table->integer('jumlah');
            $table->date('tanggal_transfer');
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfer_rak');
    }
};
