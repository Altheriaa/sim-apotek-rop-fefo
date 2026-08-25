<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('obat', function (Blueprint $table) {
            $table->dropColumn('kategori');
            $table->enum('kategori', ['Obat Bebas', 'Obat Keras', 'Obat Prekursor'])->nullable()->after('nama_obat');
        });
    }

    public function down(): void
    {
        Schema::table('obat', function (Blueprint $table) {
            $table->dropColumn('kategori');
            $table->string('kategori')->nullable();
        });
    }
};
