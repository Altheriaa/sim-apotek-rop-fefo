<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel header transaksi penjualan kasir (POS)
        Schema::create('penjualan', function (Blueprint $table) {
            $table->id();
            $table->string('no_transaksi')->unique(); // Contoh: TRX-20260814-001
            $table->foreignId('user_id')->constrained('users'); // Kasir
            $table->dateTime('tanggal_transaksi');
            $table->decimal('total_harga', 14, 2);
            $table->decimal('nominal_bayar', 14, 2);
            $table->decimal('kembalian', 14, 2);
            $table->string('nama_pembeli')->nullable()->default('Umum');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // Tabel detail item per transaksi — potong stok_rak via FEFO
        Schema::create('detail_penjualan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penjualan_id')->constrained('penjualan')->cascadeOnDelete();
            $table->foreignId('obat_id')->constrained('obat');
            $table->foreignId('obat_batch_id')->constrained('obat_batch'); // Batch rak terpakai (FEFO)
            $table->integer('jumlah');
            $table->decimal('harga_satuan', 12, 2);
            $table->decimal('subtotal', 14, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_penjualan');
        Schema::dropIfExists('penjualan');
    }
};
