<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('obat', function (Blueprint $table) {
            // Lead time: estimasi hari pengiriman supplier (default 3 hari)
            $table->unsignedTinyInteger('lead_time_hari')
                ->default(3)
                ->after('rop_minimum')
                ->comment('Perkiraan hari pengiriman supplier (untuk hitung ROP dinamis)');
        });
    }

    public function down(): void
    {
        Schema::table('obat', function (Blueprint $table) {
            $table->dropColumn('lead_time_hari');
        });
    }
};
