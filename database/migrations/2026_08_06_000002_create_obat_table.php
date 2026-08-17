<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('obat', function (Blueprint $table) {
            $table->id();
            $table->string('kode_obat')->unique()->nullable();
            $table->string('nama_obat');
            $table->string('kategori')->nullable();
            $table->string('satuan');
            $table->decimal('harga', 12, 2)->default(0);       // Harga jual ke pasien
            $table->integer('rop_minimum')->default(10);        // Titik pemesanan ulang ke supplier
            $table->integer('min_stok_rak')->default(5);        // Batas minimum stok di display rak
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('obat');
    }
};
