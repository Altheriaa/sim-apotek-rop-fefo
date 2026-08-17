<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('obat_batch', function (Blueprint $table) {
            $table->id();
            $table->foreignId('obat_id')->constrained('obat')->cascadeOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained('supplier')->nullOnDelete();
            $table->string('nomor_batch')->nullable();
            $table->date('tanggal_masuk');
            $table->date('tanggal_kadaluwarsa');          // Kunci utama FEFO
            $table->integer('stok_awal');
            $table->integer('stok_gudang')->default(0);   // Sisa stok di gudang fisik
            $table->integer('stok_rak')->default(0);      // Sisa stok di display rak
            $table->decimal('harga_beli', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('obat_batch');
    }
};
