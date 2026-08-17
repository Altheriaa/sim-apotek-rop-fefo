<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pesanan')->unique();
            $table->foreignId('supplier_id')->constrained('supplier');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->date('tanggal_pesan');
            $table->enum('status', ['draft', 'diproses', 'dikirim', 'selesai', 'batal'])->default('draft');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('detail_pesanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanan')->cascadeOnDelete();
            $table->foreignId('obat_id')->constrained('obat');
            $table->integer('jumlah_pesan');
            $table->decimal('estimasi_harga', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_pesanan');
        Schema::dropIfExists('pesanan');
    }
};
