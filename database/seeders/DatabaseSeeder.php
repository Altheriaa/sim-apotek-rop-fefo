<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Supplier;
use App\Models\Obat;
use App\Models\ObatBatch;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ── Users ──
        User::create([
            'nama_user' => 'Administrator',
            'username'  => 'admin',
            'email'  => 'admin@gmail.com',
            'password'  => 'password',
            'role'      => 'admin',
        ]);

        User::create([
            'nama_user' => 'Karyawan Apotek',
            'username'  => 'karyawan',
            'email'  => 'karyawan@gmail.com',
            'password'  => 'password',
            'role'      => 'karyawan',
        ]);

        // ── Supplier ──
        $supplier1 = Supplier::create([
            'nama_supplier' => 'PT. Kimia Farma',
            'alamat'        => 'Jl. Veteran No. 9, Jakarta Pusat',
            'kontak'        => '021-4201234',
        ]);

        $supplier2 = Supplier::create([
            'nama_supplier' => 'PT. Kalbe Farma',
            'alamat'        => 'Jl. Let. Jend. Suprapto Kav. 4, Jakarta',
            'kontak'        => '021-4287345',
        ]);

        $supplier3 = Supplier::create([
            'nama_supplier' => 'PT. Sanbe Farma',
            'alamat'        => 'Jl. Tamansari No. 10, Bandung',
            'kontak'        => '022-2034567',
        ]);

        // ── Obat ──
        $obat1 = Obat::create([
            'nama_obat'   => 'Paracetamol 500mg',
            'satuan'      => 'Tablet',
            'rop_minimum' => 50,
        ]);

        $obat2 = Obat::create([
            'nama_obat'   => 'Amoxicillin 500mg',
            'satuan'      => 'Kapsul',
            'rop_minimum' => 30,
        ]);

        $obat3 = Obat::create([
            'nama_obat'   => 'Omeprazole 20mg',
            'satuan'      => 'Kapsul',
            'rop_minimum' => 20,
        ]);

        $obat4 = Obat::create([
            'nama_obat'   => 'Cetirizine 10mg',
            'satuan'      => 'Tablet',
            'rop_minimum' => 25,
        ]);

        $obat5 = Obat::create([
            'nama_obat'   => 'Metformin 500mg',
            'satuan'      => 'Tablet',
            'rop_minimum' => 40,
        ]);

        // ── Obat Batch (contoh data FEFO) ──
        // Paracetamol: 2 batch dengan ED berbeda
        ObatBatch::create([
            'obat_id'             => $obat1->id,
            'supplier_id'         => $supplier1->id,
            'nomor_batch'         => 'PCT-2026-001',
            'tanggal_masuk'       => '2026-07-01',
            'tanggal_kadaluwarsa' => '2027-07-01',
            'stok_awal'           => 100,
            'stok_sisa'           => 80,
        ]);

        ObatBatch::create([
            'obat_id'             => $obat1->id,
            'supplier_id'         => $supplier1->id,
            'nomor_batch'         => 'PCT-2026-002',
            'tanggal_masuk'       => '2026-08-01',
            'tanggal_kadaluwarsa' => '2028-02-01',
            'stok_awal'           => 100,
            'stok_sisa'           => 100,
        ]);

        // Amoxicillin: 1 batch mendekati ED
        ObatBatch::create([
            'obat_id'             => $obat2->id,
            'supplier_id'         => $supplier2->id,
            'nomor_batch'         => 'AMX-2025-010',
            'tanggal_masuk'       => '2025-12-15',
            'tanggal_kadaluwarsa' => '2026-09-01',
            'stok_awal'           => 50,
            'stok_sisa'           => 35,
        ]);

        // Omeprazole
        ObatBatch::create([
            'obat_id'             => $obat3->id,
            'supplier_id'         => $supplier2->id,
            'nomor_batch'         => 'OMP-2026-005',
            'tanggal_masuk'       => '2026-06-01',
            'tanggal_kadaluwarsa' => '2028-06-01',
            'stok_awal'           => 60,
            'stok_sisa'           => 45,
        ]);

        // Cetirizine: stok mendekati ROP
        ObatBatch::create([
            'obat_id'             => $obat4->id,
            'supplier_id'         => $supplier3->id,
            'nomor_batch'         => 'CTZ-2026-003',
            'tanggal_masuk'       => '2026-05-01',
            'tanggal_kadaluwarsa' => '2027-11-01',
            'stok_awal'           => 40,
            'stok_sisa'           => 10, // di bawah ROP 25!
        ]);

        // Metformin
        ObatBatch::create([
            'obat_id'             => $obat5->id,
            'supplier_id'         => $supplier1->id,
            'nomor_batch'         => 'MTF-2026-007',
            'tanggal_masuk'       => '2026-07-15',
            'tanggal_kadaluwarsa' => '2028-01-15',
            'stok_awal'           => 200,
            'stok_sisa'           => 150,
        ]);
    }
}
