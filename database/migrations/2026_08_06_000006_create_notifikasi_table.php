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
            $table->foreignId('obat_id')->nullable()->constrained('obat')->nullOnDelete();
            $table->string('jenis_notifikasi'); // stok_menipis | mendekati_kadaluwarsa
            $table->text('pesan');
            $table->string('target_nomor');
            $table->string('status')->default('pending'); // pending | terkirim | gagal
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
