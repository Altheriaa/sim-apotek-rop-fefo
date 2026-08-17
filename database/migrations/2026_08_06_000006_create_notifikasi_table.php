<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('obat_id')->nullable()->constrained('obat');
            $table->enum('jenis_notifikasi', ['stok_menipis', 'mendekati_kadaluwarsa', 'restock_rak']);
            $table->text('pesan');
            $table->string('target_nomor');
            $table->enum('status', ['pending', 'terkirim', 'gagal'])->default('pending');
            $table->string('fonnte_id')->nullable();
            $table->timestamp('dikirim_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};
