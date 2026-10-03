<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Pesanan;
use App\Models\Penjualan;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CodeGenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_pesanan_code_generation_handles_gaps_and_never_duplicates()
    {
        $supplier = Supplier::create([
            'nama_supplier' => 'PT Test Supplier',
            'no_telepon'    => '08123456789',
            'alamat'        => 'Jl. Test No. 1',
        ]);

        $user = User::factory()->create();

        // 1. Initial code
        $code1 = Pesanan::generateKodePesanan('2026-10-02');
        $this->assertEquals('PO-20261002-0001', $code1);

        $pesanan1 = Pesanan::create([
            'kode_pesanan'  => $code1,
            'supplier_id'   => $supplier->id,
            'user_id'       => $user->id,
            'tanggal_pesan' => '2026-10-02',
            'status'        => 'draft',
        ]);

        // 2. Second code
        $code2 = Pesanan::generateKodePesanan('2026-10-02');
        $this->assertEquals('PO-20261002-0002', $code2);

        $pesanan2 = Pesanan::create([
            'kode_pesanan'  => $code2,
            'supplier_id'   => $supplier->id,
            'user_id'       => $user->id,
            'tanggal_pesan' => '2026-10-02',
            'status'        => 'draft',
        ]);

        // 3. Third code
        $code3 = Pesanan::generateKodePesanan('2026-10-02');
        $this->assertEquals('PO-20261002-0003', $code3);

        $pesanan3 = Pesanan::create([
            'kode_pesanan'  => $code3,
            'supplier_id'   => $supplier->id,
            'user_id'       => $user->id,
            'tanggal_pesan' => '2026-10-02',
            'status'        => 'draft',
        ]);

        // Simulate deleting order 2 (this caused count() to become 2 in production!)
        $pesanan2->delete();

        // Next generated code must be 0004, NOT 0003!
        $code4 = Pesanan::generateKodePesanan('2026-10-02');
        $this->assertEquals('PO-20261002-0004', $code4);
    }

    public function test_penjualan_code_generation_handles_gaps_and_never_duplicates()
    {
        $user = User::factory()->create();

        $trx1 = Penjualan::generateNoTransaksi('2026-10-02');
        $this->assertEquals('TRX-20261002-0001', $trx1);

        $p1 = Penjualan::create([
            'no_transaksi'      => $trx1,
            'user_id'           => $user->id,
            'tanggal_transaksi' => '2026-10-02 10:00:00',
            'total_harga'       => 10000,
            'nominal_bayar'     => 10000,
            'kembalian'         => 0,
        ]);

        $trx2 = Penjualan::generateNoTransaksi('2026-10-02');
        $this->assertEquals('TRX-20261002-0002', $trx2);

        $p2 = Penjualan::create([
            'no_transaksi'      => $trx2,
            'user_id'           => $user->id,
            'tanggal_transaksi' => '2026-10-02 11:00:00',
            'total_harga'       => 20000,
            'nominal_bayar'     => 20000,
            'kembalian'         => 0,
        ]);

        $trx3 = Penjualan::generateNoTransaksi('2026-10-02');
        $this->assertEquals('TRX-20261002-0003', $trx3);

        $p3 = Penjualan::create([
            'no_transaksi'      => $trx3,
            'user_id'           => $user->id,
            'tanggal_transaksi' => '2026-10-02 12:00:00',
            'total_harga'       => 30000,
            'nominal_bayar'     => 30000,
            'kembalian'         => 0,
        ]);

        // Delete transaction 2
        $p2->delete();

        // Next code must be 0004
        $trx4 = Penjualan::generateNoTransaksi('2026-10-02');
        $this->assertEquals('TRX-20261002-0004', $trx4);
    }
}
