<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('obat_keluar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('obat_id')->constrained('obat')->cascadeOnDelete();
            $table->foreignId('obat_batch_id')->constrained('obat_batch')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->date('tanggal_keluar');
            $table->integer('jumlah');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('obat_keluar');
    }
};
