<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\ObatBatch;
use App\Models\Penjualan;
use App\Models\TransferRak;
use App\Models\Notifikasi;
use App\Models\Pesanan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // ── Statistik utama (multi-lokasi) ──
        $totalObat      = Obat::count();
        $totalStokGudang = (int) ObatBatch::sum('stok_gudang');
        $totalStokRak   = (int) ObatBatch::sum('stok_rak');
        $totalStok      = $totalStokGudang + $totalStokRak;

        // Obat kritis: Total Apotek (gudang+rak) ≤ ROP
        $obatKritisCount = 0;
        $obatList        = Obat::where('rop_minimum', '>', 0)->get();
        foreach ($obatList as $obat) {
            if ($obat->stok_total <= $obat->rop_minimum) {
                $obatKritisCount++;
            }
        }

        // Obat yang perlu restock rak (stok_rak ≤ min_stok_rak namun gudang masih ada)
        $rakKritisCount = Obat::where('min_stok_rak', '>', 0)
            ->whereRaw('(SELECT COALESCE(SUM(stok_rak),0) FROM obat_batch WHERE obat_batch.obat_id = obat.id) <= obat.min_stok_rak')
            ->whereRaw('(SELECT COALESCE(SUM(stok_gudang),0) FROM obat_batch WHERE obat_batch.obat_id = obat.id) > 0')
            ->count();

        // Batch mendekati kadaluwarsa (≤ 30 hari, masih ada stok)
        $batchEdCount = ObatBatch::where(function ($q) {
                $q->where('stok_gudang', '>', 0)->orWhere('stok_rak', '>', 0);
            })
            ->where('tanggal_kadaluwarsa', '<=', now()->addDays(30))
            ->count();

        // Penjualan 7 hari terakhir (untuk chart)
        $penjualanChart = Penjualan::selectRaw('DATE(tanggal_transaksi) as tanggal, SUM(total_harga) as total, COUNT(*) as jumlah_transaksi')
            ->where('tanggal_transaksi', '>=', now()->subDays(7))
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        // Transfer rak hari ini
        $transferHariIni = TransferRak::whereDate('tanggal_transfer', today())->sum('jumlah');

        // Notifikasi terbaru
        $notifikasiTerbaru = Notifikasi::with('obat')
            ->latest()
            ->take(5)
            ->get();

        // Pesanan aktif (bukan selesai/batal)
        $pesananAktif = Pesanan::with('supplier')
            ->whereIn('status', ['draft', 'diproses', 'dikirim'])
            ->latest()
            ->take(5)
            ->get();

        // Aktivitas terakhir — gabungan transfer rak & penjualan
        $transferTerakhir = TransferRak::with(['obat', 'user'])
            ->latest()
            ->take(5)
            ->get();

        $penjualanTerakhir = Penjualan::with(['user', 'details.obat'])
            ->latest()
            ->take(5)
            ->get();

        return view('pages.dashboard', [
            'title'              => 'Dashboard',
            'totalObat'          => $totalObat,
            'totalStok'          => $totalStok,
            'totalStokGudang'    => $totalStokGudang,
            'totalStokRak'       => $totalStokRak,
            'obatKritisCount'    => $obatKritisCount,
            'rakKritisCount'     => $rakKritisCount,
            'batchEdCount'       => $batchEdCount,
            'penjualanChart'     => $penjualanChart,
            'transferHariIni'    => $transferHariIni,
            'notifikasiTerbaru'  => $notifikasiTerbaru,
            'pesananAktif'       => $pesananAktif,
            'transferTerakhir'   => $transferTerakhir,
            'penjualanTerakhir'  => $penjualanTerakhir,
        ]);
    }
}
