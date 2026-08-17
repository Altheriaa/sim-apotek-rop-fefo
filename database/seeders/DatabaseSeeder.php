<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Supplier;
use App\Models\Obat;
use App\Models\ObatBatch;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Users ──
        User::create([
            'nama_user' => 'Administrator',
            'username'  => 'admin',
            'email'     => 'admin@gmail.com',
            'password'  => 'password',
            'role'      => 'admin',
        ]);

        User::create([
            'nama_user' => 'Karyawan Apotek',
            'username'  => 'karyawan',
            'email'     => 'karyawan@gmail.com',
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

        // ── Master Obat ──
        $obat1 = Obat::create([
            'kode_obat'    => 'OBT-001',
            'nama_obat'    => 'Paracetamol 500mg',
            'kategori'     => 'Analgesik',
            'satuan'       => 'Tablet',
            'harga'        => 2500,
            'rop_minimum'  => 50,
            'min_stok_rak' => 10,
        ]);

        $obat2 = Obat::create([
            'kode_obat'    => 'OBT-002',
            'nama_obat'    => 'Amoxicillin 500mg',
            'kategori'     => 'Antibiotik',
            'satuan'       => 'Kapsul',
            'harga'        => 4500,
            'rop_minimum'  => 30,
            'min_stok_rak' => 8,
        ]);

        $obat3 = Obat::create([
            'kode_obat'    => 'OBT-003',
            'nama_obat'    => 'Omeprazole 20mg',
            'kategori'     => 'Lambung',
            'satuan'       => 'Kapsul',
            'harga'        => 5000,
            'rop_minimum'  => 20,
            'min_stok_rak' => 5,
        ]);

        $obat4 = Obat::create([
            'kode_obat'    => 'OBT-004',
            'nama_obat'    => 'Cetirizine 10mg',
            'kategori'     => 'Antihistamin',
            'satuan'       => 'Tablet',
            'harga'        => 3500,
            'rop_minimum'  => 25,
            'min_stok_rak' => 5,
        ]);

        $obat5 = Obat::create([
            'kode_obat'    => 'OBT-005',
            'nama_obat'    => 'Metformin 500mg',
            'kategori'     => 'Diabetes',
            'satuan'       => 'Tablet',
            'harga'        => 3000,
            'rop_minimum'  => 40,
            'min_stok_rak' => 10,
        ]);

        // ── Obat Batch (Multi-Lokasi: stok_gudang + stok_rak) ──
        // Paracetamol: 2 batch, ED berbeda. Batch 1 lebih dekat ED → FEFO prioritas
        ObatBatch::create([
            'obat_id'             => $obat1->id,
            'supplier_id'         => $supplier1->id,
            'nomor_batch'         => 'PCT-2026-001',
            'tanggal_masuk'       => '2026-07-01',
            'tanggal_kadaluwarsa' => '2027-07-01',
            'stok_awal'           => 100,
            'stok_gudang'         => 60,  // Masih di gudang
            'stok_rak'            => 20,  // Sudah di rak
            'harga_beli'          => 2000,
        ]);

        ObatBatch::create([
            'obat_id'             => $obat1->id,
            'supplier_id'         => $supplier1->id,
            'nomor_batch'         => 'PCT-2026-002',
            'tanggal_masuk'       => '2026-08-01',
            'tanggal_kadaluwarsa' => '2028-02-01',
            'stok_awal'           => 100,
            'stok_gudang'         => 100,
            'stok_rak'            => 0,
            'harga_beli'          => 2000,
        ]);

        // Amoxicillin: 1 batch, mendekati ED (August 2026)
        ObatBatch::create([
            'obat_id'             => $obat2->id,
            'supplier_id'         => $supplier2->id,
            'nomor_batch'         => 'AMX-2025-010',
            'tanggal_masuk'       => '2025-12-15',
            'tanggal_kadaluwarsa' => '2026-09-15',
            'stok_awal'           => 50,
            'stok_gudang'         => 25,
            'stok_rak'            => 10,
            'harga_beli'          => 3500,
        ]);

        // Omeprazole
        ObatBatch::create([
            'obat_id'             => $obat3->id,
            'supplier_id'         => $supplier2->id,
            'nomor_batch'         => 'OMP-2026-005',
            'tanggal_masuk'       => '2026-06-01',
            'tanggal_kadaluwarsa' => '2028-06-01',
            'stok_awal'           => 60,
            'stok_gudang'         => 35,
            'stok_rak'            => 10,
            'harga_beli'          => 4000,
        ]);

        // Cetirizine: stok rak kritis (stok_rak ≤ min_stok_rak=5)
        ObatBatch::create([
            'obat_id'             => $obat4->id,
            'supplier_id'         => $supplier3->id,
            'nomor_batch'         => 'CTZ-2026-003',
            'tanggal_masuk'       => '2026-05-01',
            'tanggal_kadaluwarsa' => '2027-11-01',
            'stok_awal'           => 40,
            'stok_gudang'         => 5,   // Hampir habis di gudang
            'stok_rak'            => 3,   // Kritis! ≤ min_stok_rak (5)
            'harga_beli'          => 2800,
        ]);

        // Metformin
        ObatBatch::create([
            'obat_id'             => $obat5->id,
            'supplier_id'         => $supplier1->id,
            'nomor_batch'         => 'MTF-2026-007',
            'tanggal_masuk'       => '2026-07-15',
            'tanggal_kadaluwarsa' => '2028-01-15',
            'stok_awal'           => 200,
            'stok_gudang'         => 130,
            'stok_rak'            => 20,
            'harga_beli'          => 2500,
        ]);
    }
}
