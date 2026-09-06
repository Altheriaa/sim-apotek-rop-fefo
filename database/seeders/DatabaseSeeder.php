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

        Obat::create([
            'kode_obat'       => 'OBT-002',
            'nama_obat'       => 'PARAMEX TAB STR 4S',
            'kategori'        => 'Obat Bebas',
            'satuan_beli'     => 'Box',
            'satuan_jual'     => 'Strip',
            'isi_per_kemasan' => 50,
            'harga_jual'      => 2300,
            'rop_minimum'     => 1,   
            'min_stok_rak'    => 50,  
        ]);

        Obat::create([
            'kode_obat'       => 'OBT-003',
            'nama_obat'       => 'FARSIFEN 400MG 100S',
            'kategori'        => 'Obat Keras',
            'satuan_beli'     => 'Box',
            'satuan_jual'     => 'Strip',
            'isi_per_kemasan' => 10,
            'harga_jual'      => 5016,
            'rop_minimum'     => 2,   
            'min_stok_rak'    => 20,  //satuan_jual
        ]);

        Obat::create([
            'kode_obat'       => 'OBT-004',
            'nama_obat'       => 'FARSIFEN PLUS',
            'kategori'        => 'Obat Bebas',
            'satuan_beli'     => 'Box',
            'satuan_jual'     => 'Strip',
            'isi_per_kemasan' => 10,
            'harga_jual'      => 3550,
            'rop_minimum'     => 1,   
            'min_stok_rak'    => 20,  //satuan_jual
        ]);

        Obat::create([
            'kode_obat'       => 'OBT-005',
            'nama_obat'       => 'MOLEXFLU TAB 150S',
            'kategori'        => 'Obat Prekursor',
            'satuan_beli'     => 'Box',
            'satuan_jual'     => 'Strip',
            'isi_per_kemasan' => 15,
            'harga_jual'      => 5000,
            'rop_minimum'     => 1,   
            'min_stok_rak'    => 30,  //satuan_jual
        ]);

        Obat::create([
            'kode_obat'       => 'OBT-006',
            'nama_obat'       => 'CETIRIZINE 10MG TAB',
            'kategori'        => 'Obat Prekursor',
            'satuan_beli'     => 'Box',
            'satuan_jual'     => 'Strip',
            'isi_per_kemasan' => 10,
            'harga_jual'      => 210,
            'rop_minimum'     => 2,   
            'min_stok_rak'    => 40,  //satuan_jual
        ]);

        Obat::create([
            'kode_obat'       => 'OBT-007',
            'nama_obat'       => 'SANMOL 500MG TABS',
            'kategori'        => 'Obat Bebas',
            'satuan_beli'     => 'Box',
            'satuan_jual'     => 'Strip',
            'isi_per_kemasan' => 25,
            'harga_jual'      => 1620,
            'rop_minimum'     => 1,   
            'min_stok_rak'    => 50,  //satuan_jual
        ]);

        Obat::create([
            'kode_obat'       => 'OBT-008',
            'nama_obat'       => 'BODREXIN 80MG 18 TABLET',
            'kategori'        => 'Obat Bebas',
            'satuan_beli'     => 'Box',
            'satuan_jual'     => 'Strip',
            'isi_per_kemasan' => 3,
            'harga_jual'      => 4430,
            'rop_minimum'     => 5,   
            'min_stok_rak'    => 9,  //satuan_jual
        ]);

        Obat::create([
            'kode_obat'       => 'OBT-009',
            'nama_obat'       => 'BODREX TAB 20S',
            'kategori'        => 'Obat Bebas',
            'satuan_beli'     => 'Box',
            'satuan_jual'     => 'Strip',
            'isi_per_kemasan' => 2,
            'harga_jual'      => 4050,
            'rop_minimum'     => 2,   
            'min_stok_rak'    => 10,  //satuan_jual
        ]);

        Obat::create([
            'kode_obat'       => 'OBT-010',
            'nama_obat'       => 'BODREX EXTRA TAB 4S',
            'kategori'        => 'Obat Bebas',
            'satuan_beli'     => 'Box',
            'satuan_jual'     => 'Strip',
            'isi_per_kemasan' => 25,
            'harga_jual'      => 2120,
            'rop_minimum'     => 1,   
            'min_stok_rak'    => 25,  //satuan_jual
        ]);

        Obat::create([
            'kode_obat'       => 'OBT-011',
            'nama_obat'       => 'BODREX FLU & BATUK BERDAHAK PE 4S',
            'kategori'        => 'Obat Bebas',
            'satuan_beli'     => 'Box',
            'satuan_jual'     => 'Strip',
            'isi_per_kemasan' => 25,
            'harga_jual'      => 1900,
            'rop_minimum'     => 1,   
            'min_stok_rak'    => 25,  //satuan_jual
        ]);

        Obat::create([
            'kode_obat'       => 'OBT-012',
            'nama_obat'       => 'HUFAGRIP FORTE TAB 100S',
            'kategori'        => 'Obat Prekursor',
            'satuan_beli'     => 'Box',
            'satuan_jual'     => 'Strip',
            'isi_per_kemasan' => 10,
            'harga_jual'      => 4800,
            'rop_minimum'     => 1,   
            'min_stok_rak'    => 20,  //satuan_jual
        ]);

        Obat::create([
            'kode_obat'       => 'OBT-013',
            'nama_obat'       => 'ANTASIDA SUSP 60ML',
            'kategori'        => 'Obat Bebas',
            'satuan_beli'     => 'Botol',
            'satuan_jual'     => 'Botol',
            'isi_per_kemasan' => 1,
            'harga_jual'      => 6500,
            'rop_minimum'     => 3,   
            'min_stok_rak'    => 3,  //satuan_jual
        ]);

        Obat::create([
            'kode_obat'       => 'OBT-014',
            'nama_obat'       => 'ANTASIDA DOEN 400MG TAB',
            'kategori'        => 'Obat Bebas',
            'satuan_beli'     => 'Box',
            'satuan_jual'     => 'Strip',
            'isi_per_kemasan' => 10,
            'harga_jual'      => 1260,
            'rop_minimum'     => 5,   
            'min_stok_rak'    => 30,  //satuan_jual
        ]);

        Obat::create([
            'kode_obat'       => 'OBT-015',
            'nama_obat'       => 'OMEPRAZOLE 20MG',
            'kategori'        => 'Obat Keras',
            'satuan_beli'     => 'Box',
            'satuan_jual'     => 'Strip',
            'isi_per_kemasan' => 20,
            'harga_jual'      => 360,
            'rop_minimum'     => 2,   
            'min_stok_rak'    => 40,  //satuan_jual
        ]);

        Obat::create([
            'kode_obat'       => 'OBT-016',
            'nama_obat'       => 'LANSOPRAZOLE 30MG',
            'kategori'        => 'Obat Keras',
            'satuan_beli'     => 'Box',
            'satuan_jual'     => 'Strip',
            'isi_per_kemasan' => 10,
            'harga_jual'      => 890,
            'rop_minimum'     => 2,   
            'min_stok_rak'    => 20,  //satuan_jual
        ]);

        Obat::create([
            'kode_obat'       => 'OBT-017',
            'nama_obat'       => 'POLYSILANE CHEW TAB 40S',
            'kategori'        => 'Obat Bebas',
            'satuan_beli'     => 'Box',
            'satuan_jual'     => 'Strip',
            'isi_per_kemasan' => 5,
            'harga_jual'      => 8389,
            'rop_minimum'     => 1,   
            'min_stok_rak'    => 10,  //satuan_jual
        ]);

        Obat::create([
            'kode_obat'       => 'OBT-018',
            'nama_obat'       => 'POLYSILANE SYR 100ML',
            'kategori'        => 'Obat Bebas',
            'satuan_beli'     => 'Botol',
            'satuan_jual'     => 'Botol',
            'isi_per_kemasan' => 1,
            'harga_jual'      => 22080,
            'rop_minimum'     => 2,   
            'min_stok_rak'    => 2,  //satuan_jual
        ]);

        Obat::create([
            'kode_obat'       => 'OBT-019',
            'nama_obat'       => 'POLYSILANE SYR 180ML',
            'kategori'        => 'Obat Bebas',
            'satuan_beli'     => 'Botol',
            'satuan_jual'     => 'Botol',
            'isi_per_kemasan' => 1,
            'harga_jual'      => 32010,
            'rop_minimum'     => 2,   
            'min_stok_rak'    => 2,  //satuan_jual
        ]);

        Obat::create([
            'kode_obat'       => 'OBT-020',
            'nama_obat'       => 'RANITIDINE 150MG TAB 100S',
            'kategori'        => 'Obat Keras',
            'satuan_beli'     => 'Box',
            'satuan_jual'     => 'Strip',
            'isi_per_kemasan' => 10,
            'harga_jual'      => 210,
            'rop_minimum'     => 2,   
            'min_stok_rak'    => 20,  //satuan_jual
        ]);

        Obat::create([
            'kode_obat'       => 'OBT-021',
            'nama_obat'       => 'AMOXICILLIN 500MG TAB 100S',
            'kategori'        => 'Obat Keras',
            'satuan_beli'     => 'Box',
            'satuan_jual'     => 'Strip',
            'isi_per_kemasan' => 10,
            'harga_jual'      => 4010,
            'rop_minimum'     => 2,   
            'min_stok_rak'    => 20,  //satuan_jual
        ]);

        Obat::create([
            'kode_obat'       => 'OBT-022',
            'nama_obat'       => 'METHYLPREDNISOLONE 4MG TAB 100S',
            'kategori'        => 'Obat Keras',
            'satuan_beli'     => 'Box',
            'satuan_jual'     => 'Strip',
            'isi_per_kemasan' => 10,
            'harga_jual'      => 210,
            'rop_minimum'     => 2,   
            'min_stok_rak'    => 20,  //satuan_jual
        ]);

        Obat::create([
            'kode_obat'       => 'OBT-023',
            'nama_obat'       => 'ALLOPURINOL 100MG TAB 100S',
            'kategori'        => 'Obat Keras',
            'satuan_beli'     => 'Box',
            'satuan_jual'     => 'Strip',
            'isi_per_kemasan' => 10,
            'harga_jual'      => 2140,
            'rop_minimum'     => 2,   
            'min_stok_rak'    => 20,  //satuan_jual
        ]);

        Obat::create([
            'kode_obat'       => 'OBT-024',
            'nama_obat'       => 'AMLODIPINE 5MG TAB',
            'kategori'        => 'Obat Keras',
            'satuan_beli'     => 'Box',
            'satuan_jual'     => 'Strip',
            'isi_per_kemasan' => 10,
            'harga_jual'      => 400,
            'rop_minimum'     => 2,   
            'min_stok_rak'    => 20,  //satuan_jual
        ]);

        Obat::create([
            'kode_obat'       => 'OBT-025',
            'nama_obat'       => 'AMLODIPINE 10MG TAB',
            'kategori'        => 'Obat Keras',
            'satuan_beli'     => 'Box',
            'satuan_jual'     => 'Strip',
            'isi_per_kemasan' => 10,
            'harga_jual'      => 900,
            'rop_minimum'     => 2,   
            'min_stok_rak'    => 20,  //satuan_jual
        ]);

        Obat::create([
            'kode_obat'       => 'OBT-026',
            'nama_obat'       => 'PARACETAMOL SYR 60ML',
            'kategori'        => 'Obat Bebas',
            'satuan_beli'     => 'Botol',
            'satuan_jual'     => 'Botol',
            'isi_per_kemasan' => 1,
            'harga_jual'      => 4760,
            'rop_minimum'     => 3,   
            'min_stok_rak'    => 3,  //satuan_jual
        ]);

        Obat::create([
            'kode_obat'       => 'OBT-027',
            'nama_obat'       => 'LERZIN 10MG CAP 50S',
            'kategori'        => 'Obat Keras',
            'satuan_beli'     => 'Box',
            'satuan_jual'     => 'Strip',
            'isi_per_kemasan' => 5,
            'harga_jual'      => 4760,
            'rop_minimum'     => 2,   
            'min_stok_rak'    => 10,  //satuan_jual
        ]);

        Obat::create([
            'kode_obat'       => 'OBT-028',
            'nama_obat'       => 'LERZIN SYR 60ML',
            'kategori'        => 'Obat Keras',
            'satuan_beli'     => 'Botol',
            'satuan_jual'     => 'Botol',
            'isi_per_kemasan' => 1,
            'harga_jual'      => 6790,
            'rop_minimum'     => 3,   
            'min_stok_rak'    => 2,  //satuan_jual
        ]);

        Obat::create([
            'kode_obat'       => 'OBT-029',
            'nama_obat'       => 'SANMOL 120MG/5ML SYR 60ML',
            'kategori'        => 'Obat Bebas',
            'satuan_beli'     => 'Botol',
            'satuan_jual'     => 'Botol',
            'isi_per_kemasan' => 1,
            'harga_jual'      => 16560,
            'rop_minimum'     => 5,   
            'min_stok_rak'    => 2,  //satuan_jual
        ]);

        Obat::create([
            'kode_obat'       => 'OBT-030',
            'nama_obat'       => 'SANMOL 60MG/0,6ML DROP 15ML',
            'kategori'        => 'Obat Bebas',
            'satuan_beli'     => 'Botol',
            'satuan_jual'     => 'Botol',
            'isi_per_kemasan' => 1,
            'harga_jual'      => 20400,
            'rop_minimum'     => 5,   
            'min_stok_rak'    => 2,  //satuan_jual
        ]);

        Obat::create([
            'kode_obat'       => 'OBT-031',
            'nama_obat'       => 'BODREX MIGRA STR 4S',
            'kategori'        => 'Obat Bebas',
            'satuan_beli'     => 'Box',
            'satuan_jual'     => 'Strip',
            'isi_per_kemasan' => 15,
            'harga_jual'      => 2230,
            'rop_minimum'     => 1,   
            'min_stok_rak'    => 25,  //satuan_jual
        ]);

        Obat::create([
            'kode_obat'       => 'OBT-032',
            'nama_obat'       => 'BODREX FLU & BATUK KERING STR 4S',
            'kategori'        => 'Obat Prekursor',
            'satuan_beli'     => 'Box',
            'satuan_jual'     => 'Strip',
            'isi_per_kemasan' => 15,
            'harga_jual'      => 2080,
            'rop_minimum'     => 1,   
            'min_stok_rak'    => 25,  //satuan_jual
        ]);

        Obat::create([
            'kode_obat'       => 'OBT-033',
            'nama_obat'       => 'HUFAGRIP BP HIJAU SYR 60ML',
            'kategori'        => 'Obat Prekursor',
            'satuan_beli'     => 'Botol',
            'satuan_jual'     => 'Botol',
            'isi_per_kemasan' => 1,
            'harga_jual'      => 18900,
            'rop_minimum'     => 2,   
            'min_stok_rak'    => 2,  //satuan_jual
        ]);

        Obat::create([
            'kode_obat'       => 'OBT-034',
            'nama_obat'       => 'HUFAGRIP PILEK BIRU SYR 60ML',
            'kategori'        => 'Obat Prekursor',
            'satuan_beli'     => 'Botol',
            'satuan_jual'     => 'Botol',
            'isi_per_kemasan' => 1,
            'harga_jual'      => 16290,
            'rop_minimum'     => 2,   
            'min_stok_rak'    => 2,  //satuan_jual
        ]);

        Obat::create([
            'kode_obat'       => 'OBT-035',
            'nama_obat'       => 'HUFAGRIP FLU & BATUK KUNING SYR 60ML',
            'kategori'        => 'Obat Prekursor',
            'satuan_beli'     => 'Botol',
            'satuan_jual'     => 'Botol',
            'isi_per_kemasan' => 1,
            'harga_jual'      => 21510,
            'rop_minimum'     => 2,   
            'min_stok_rak'    => 2,  //satuan_jual
        ]);

        Obat::create([
            'kode_obat'       => 'OBT-036',
            'nama_obat'       => 'HUFAGRIP TMP SYR 60ML',
            'kategori'        => 'Obat Bebas',
            'satuan_beli'     => 'Botol',
            'satuan_jual'     => 'Botol',
            'isi_per_kemasan' => 1,
            'harga_jual'      => 16045,
            'rop_minimum'     => 2,   
            'min_stok_rak'    => 2,  //satuan_jual
        ]);

        Obat::create([
            'kode_obat'       => 'OBT-037',
            'nama_obat'       => 'MIXAGRIP FLU & BATUK TAB 100S',
            'kategori'        => 'Obat Prekursor',
            'satuan_beli'     => 'Box',
            'satuan_jual'     => 'Strip',
            'isi_per_kemasan' => 25,
            'harga_jual'      => 2490,
            'rop_minimum'     => 1,   
            'min_stok_rak'    => 50,  //satuan_jual
        ]);

        Obat::create([
            'kode_obat'       => 'OBT-038',
            'nama_obat'       => 'MIXAGRIP FLU STR 4S ',
            'kategori'        => 'Obat Prekursor',
            'satuan_beli'     => 'Box',
            'satuan_jual'     => 'Strip',
            'isi_per_kemasan' => 25,
            'harga_jual'      => 2440,
            'rop_minimum'     => 1,   
            'min_stok_rak'    => 50,  //satuan_jual
        ]);

        Obat::create([
            'kode_obat'       => 'OBT-039',
            'nama_obat'       => 'KOMIX JERUK NIPIS SACH 30S',
            'kategori'        => 'Obat Bebas',
            'satuan_beli'     => 'Box',
            'satuan_jual'     => 'Sachet',
            'isi_per_kemasan' => 30,
            'harga_jual'      => 1400,
            'rop_minimum'     => 1,   
            'min_stok_rak'    => 60,  //satuan_jual
        ]);

        Obat::create([
            'kode_obat'       => 'OBT-040',
            'nama_obat'       => 'KOMIX JAHE SACH 30S',
            'kategori'        => 'Obat Bebas',
            'satuan_beli'     => 'Box',
            'satuan_jual'     => 'Sachet',
            'isi_per_kemasan' => 30,
            'harga_jual'      => 1400,
            'rop_minimum'     => 1,   
            'min_stok_rak'    => 60,  //satuan_jual
        ]);

        Obat::create([
            'kode_obat'       => 'OBT-041',
            'nama_obat'       => 'KOMIX OBH SACH 30S',
            'kategori'        => 'Obat Prekursor',
            'satuan_beli'     => 'Box',
            'satuan_jual'     => 'Sachet',
            'isi_per_kemasan' => 30,
            'harga_jual'      => 1530,
            'rop_minimum'     => 1,   
            'min_stok_rak'    => 60,  //satuan_jual
        ]);

        Obat::create([
            'kode_obat'       => 'OBT-042',
            'nama_obat'       => 'KOMIX PEPP SACH 30S',
            'kategori'        => 'Obat Bebas',
            'satuan_beli'     => 'Box',
            'satuan_jual'     => 'Sachet',
            'isi_per_kemasan' => 30,
            'harga_jual'      => 1400,
            'rop_minimum'     => 1,   
            'min_stok_rak'    => 60,  //satuan_jual
        ]);

        Obat::create([
            'kode_obat'       => 'OBT-043',
            'nama_obat'       => 'OBH COMBI JAHE SYR 100ML (GEPENG)',
            'kategori'        => 'Obat Bebas',
            'satuan_beli'     => 'Botol',
            'satuan_jual'     => 'Botol',
            'isi_per_kemasan' => 1,
            'harga_jual'      => 13800,
            'rop_minimum'     => 2,   
            'min_stok_rak'    => 2,  //satuan_jual
        ]);

        Obat::create([
            'kode_obat'       => 'OBT-044',
            'nama_obat'       => 'OBH COMBI MENTHOL SYR 100ML (GEPENG)',
            'kategori'        => 'Obat Bebas',
            'satuan_beli'     => 'Botol',
            'satuan_jual'     => 'Botol',
            'isi_per_kemasan' => 1,
            'harga_jual'      => 13640,
            'rop_minimum'     => 2,   
            'min_stok_rak'    => 2,  //satuan_jual
        ]);

        Obat::create([
            'kode_obat'       => 'OBT-045',
            'nama_obat'       => 'OBH COMBI PLUS BATUK FLU MENTHOL SYR 100ML',
            'kategori'        => 'Obat Prekursor',
            'satuan_beli'     => 'Botol',
            'satuan_jual'     => 'Botol',
            'isi_per_kemasan' => 1,
            'harga_jual'      => 18960,
            'rop_minimum'     => 2,   
            'min_stok_rak'    => 2,  //satuan_jual
        ]);

        Obat::create([
            'kode_obat'       => 'OBT-046',
            'nama_obat'       => 'OBH COMBI PLUS BATUK FLU JAHE SYR 100ML',
            'kategori'        => 'Obat Prekursor',
            'satuan_beli'     => 'Botol',
            'satuan_jual'     => 'Botol',
            'isi_per_kemasan' => 1,
            'harga_jual'      => 18580,
            'rop_minimum'     => 2,   
            'min_stok_rak'    => 2,  //satuan_jual
        ]);

        Obat::create([
            'kode_obat'       => 'OBT-047',
            'nama_obat'       => 'OBH COMBI PLUS BATUK FLU MADU SYR 100ML',
            'kategori'        => 'Obat Prekursor',
            'satuan_beli'     => 'Botol',
            'satuan_jual'     => 'Botol',
            'isi_per_kemasan' => 1,
            'harga_jual'      => 19390,
            'rop_minimum'     => 2,   
            'min_stok_rak'    => 2,  //satuan_jual
        ]);

        Obat::create([
            'kode_obat'       => 'OBT-048',
            'nama_obat'       => 'OBH COMBI PLUS BATUK FLU MENTHOL SYR 60ML',
            'kategori'        => 'Obat Bebas',
            'satuan_beli'     => 'Botol',
            'satuan_jual'     => 'Botol',
            'isi_per_kemasan' => 1,
            'harga_jual'      => 13390,
            'rop_minimum'     => 2,   
            'min_stok_rak'    => 2,  //satuan_jual
        ]);

        Obat::create([
            'kode_obat'       => 'OBT-049',
            'nama_obat'       => 'OBH COMBI PLUS BATUK FLU MADU SYR 60ML',
            'kategori'        => 'Obat Prekursor',
            'satuan_beli'     => 'Botol',
            'satuan_jual'     => 'Botol',
            'isi_per_kemasan' => 1,
            'harga_jual'      => 13130,
            'rop_minimum'     => 2,   
            'min_stok_rak'    => 2,  //satuan_jual
        ]);
    }
}
