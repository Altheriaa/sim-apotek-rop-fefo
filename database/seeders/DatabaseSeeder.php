<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Supplier;
use App\Models\Obat;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Data User ──
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

        // ── 2. Data Supplier ──
        Supplier::create([
            'nama_supplier' => 'PT. GREAT DELI FARMA',
            'alamat'        => 'JL. Tuanku Tambusai, Komplek Nangka Raya Permai, Blok F1 No. 1-7, Pekanbaru',
            'kontak'        => '0761-572045',
        ]);

        Supplier::create([
            'nama_supplier' => 'PT. PENTA VALENT TBK',
            'alamat'        => 'JL. Soekarno Hatta, Dusun Aulia, Kel. Mibo, Kec. Bandar Raya, Banda Ace',
            'kontak'        => '0651-5673891',
        ]);

        Supplier::create([
            'nama_supplier' => '⁠PT. BINA SEHAT HOSLAB',
            'alamat'        => 'JL. Williem Iskandar, Komp. MMT C, Blok L34-L35',
            'kontak'        => '061-6611943',
        ]);
        
        Supplier::create([
            'nama_supplier' => 'PT. PERUSAHAAN DAGANG TEMPO',
            'alamat'        => 'JL. Williem Iskandar, Komp. MMT C, Blok L34-L35',
            'kontak'        => '061-6611943',
        ]);

        Supplier::create([
            'nama_supplier' => '⁠PT. LAMLO PHARMACY',
            'alamat'        => 'JL. Teuku Fakinah, No. 07, Lam Blang Trieng, Darul Imarah, Aceh Besar',
            'kontak'        => '0651-6302638',
        ]);

        // ── 3. Data Master Obat (Stok Awal = 0, Tanpa Batch Transaksi) ──
        // 1. Paracetamol (1 Box = 10 Strip, ROP = 5 Box)
        Obat::create([
            'kode_obat'       => 'OBT-001',
            'nama_obat'       => 'PARACETAMOL 500MG TAB 100S',
            'kategori'        => 'Obat Bebas',
            'satuan_beli'     => 'Box',
            'satuan_jual'     => 'Strip',
            'isi_per_kemasan' => 10,
            'harga_jual'      => 2760,
            'rop_minimum'     => 2,   
            'min_stok_rak'    => 10,  
        ]);
    }
}
