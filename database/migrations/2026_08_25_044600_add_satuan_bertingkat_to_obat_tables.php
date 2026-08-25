<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Tabel Obat: Ganti satuan tunggal → satuan bertingkat ──
        Schema::table('obat', function (Blueprint $table) {
            // Tambah kolom baru
            $table->enum('satuan_beli', ['Box', 'Botol'])->default('Box')->after('kategori');
            $table->enum('satuan_jual', ['Strip', 'Botol', 'Sachet'])->default('Strip')->after('satuan_beli');
            $table->integer('isi_per_kemasan')->default(1)->after('satuan_jual');
            $table->decimal('harga_jual', 12, 2)->default(0)->after('isi_per_kemasan');
        });

        Schema::table('obat_batch', function (Blueprint $table) {
            $table->decimal('harga_beli_satuan', 12, 2)->default(0)->after('harga_beli');
        });

        // Hapus kolom lama
        Schema::table('obat', function (Blueprint $table) {
            $table->dropColumn(['satuan', 'harga']);
        });

        if (Schema::hasColumn('obat_batch', 'stok_awal')) {
            Schema::table('obat_batch', function (Blueprint $table) {
                $table->dropColumn('stok_awal');
            });
        }

        // ── 3. Tabel transfer_rak: Ganti jumlah → jumlah_keluar + jumlah_masuk_rak ──
        Schema::table('transfer_rak', function (Blueprint $table) {
            $table->integer('jumlah_keluar')->default(0)->after('user_id');
            $table->integer('jumlah_masuk_rak')->default(0)->after('jumlah_keluar');
        });

        // Hapus kolom lama
        Schema::table('transfer_rak', function (Blueprint $table) {
            $table->dropColumn('jumlah');
        });
    }

    public function down(): void
    {
        // ── Rollback transfer_rak ──
        Schema::table('transfer_rak', function (Blueprint $table) {
            $table->integer('jumlah')->default(0)->after('user_id');
        });

        Schema::table('transfer_rak', function (Blueprint $table) {
            $table->dropColumn(['jumlah_keluar', 'jumlah_masuk_rak']);
        });

        // ── Rollback obat_batch ──
        Schema::table('obat_batch', function (Blueprint $table) {
            $table->integer('stok_awal')->default(0)->after('tanggal_kadaluwarsa');
        });

        // ── Rollback obat ──
        Schema::table('obat', function (Blueprint $table) {
            $table->string('satuan')->default('Strip')->after('kategori');
            $table->decimal('harga', 12, 2)->default(0)->after('satuan');
        });

        Schema::table('obat', function (Blueprint $table) {
            $table->dropColumn(['satuan_beli', 'satuan_jual', 'isi_per_kemasan', 'harga_jual']);
        });
    }
};
