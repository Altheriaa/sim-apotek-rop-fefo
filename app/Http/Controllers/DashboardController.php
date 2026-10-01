<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\ObatBatch;
use App\Models\DetailPenjualan;
use App\Models\Penjualan;
use App\Models\TransferRak;
use App\Models\Notifikasi;
use App\Models\Pesanan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalObat       = Obat::count();
        $totalStokGudang = (int) ObatBatch::sum('stok_gudang');
        $totalStokRak    = (int) ObatBatch::sum('stok_rak');

        $penjualanPerObat = DetailPenjualan::query()
            ->whereHas('penjualan', fn($q) =>
                $q->where('tanggal_transaksi', '>=', now()->subDays(30))
            )
            ->selectRaw('obat_id, SUM(jumlah) as total_terjual')
            ->groupBy('obat_id')
            ->pluck('total_terjual', 'obat_id');

        $obatList = Obat::withSum('batches as stok_gudang_sum', 'stok_gudang')
            ->withSum('batches as stok_rak_sum', 'stok_rak')
            ->get();

        $obatKritisCount = 0;
        foreach ($obatList as $obat) {
            $d = round(($penjualanPerObat[$obat->id] ?? 0) / 30, 2);
            $l = (int) ($obat->lead_time_hari ?? 3);
            $isiPerKemasan = max((int) $obat->isi_per_kemasan, 1);

            $ropDinamis = $d > 0
                ? (int) ceil(($d * $l) / $isiPerKemasan)
                : (int) $obat->rop_minimum;

            $stokTotal = ((int) $obat->stok_gudang_sum * $isiPerKemasan) + (int) $obat->stok_rak_sum;

            if ($ropDinamis > 0 && $stokTotal <= ($ropDinamis * $isiPerKemasan)) {
                $obatKritisCount++;
            }
        }

        // Obat yang perlu restock rak (stok_rak <= min_stok_rak namun gudang masih ada)
        $rakKritisCount = Obat::where('min_stok_rak', '>', 0)
            ->whereRaw('(SELECT COALESCE(SUM(stok_rak),0) FROM obat_batch WHERE obat_batch.obat_id = obat.id) <= obat.min_stok_rak')
            ->whereRaw('(SELECT COALESCE(SUM(stok_gudang),0) FROM obat_batch WHERE obat_batch.obat_id = obat.id) > 0')
            ->count();

        // Batch mendekati kadaluwarsa (≤ 6 bulan, masih ada stok)
        $batchEdCount = ObatBatch::where(function ($q) {
                $q->where('stok_gudang', '>', 0)->orWhere('stok_rak', '>', 0);
            })
            ->where('tanggal_kadaluwarsa', '<=', now()->addMonths(6))
            ->count();

        // Penjualan 7 hari terakhir (untuk chart)
        $penjualanChart = Penjualan::selectRaw('DATE(tanggal_transaksi) as tanggal, SUM(total_harga) as total, COUNT(*) as jumlah_transaksi')
            ->where('tanggal_transaksi', '>=', now()->subDays(7))
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        // Transfer rak hari ini (dalam satuan_jual)
        $transferHariIni = TransferRak::whereDate('tanggal_transfer', today())->sum('jumlah_masuk_rak');

        // Omzet & Laba Hari Ini
        $penjualanHariIniData = Penjualan::with(['details.obat', 'details.obatBatch'])
            ->whereDate('tanggal_transaksi', today())
            ->get();
        $omzetHariIni = (float) $penjualanHariIniData->sum('total_harga');
        $labaHariIni  = (float) $penjualanHariIniData->sum(fn ($p) => $p->total_laba);

        // Omzet & Laba Bulan Ini
        $penjualanBulanIniData = Penjualan::with(['details.obat', 'details.obatBatch'])
            ->whereMonth('tanggal_transaksi', now()->month)
            ->whereYear('tanggal_transaksi', now()->year)
            ->get();
        $omzetBulanIni = (float) $penjualanBulanIniData->sum('total_harga');
        $labaBulanIni  = (float) $penjualanBulanIniData->sum(fn ($p) => $p->total_laba);

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

        $transferTerakhir = TransferRak::with(['obat', 'user'])
            ->latest()
            ->take(5)
            ->get();

        $penjualanTerakhir = Penjualan::with(['user', 'details.obat', 'details.obatBatch'])
            ->latest()
            ->take(5)
            ->get();

        return view('pages.dashboard', [
            'title'             => 'Dashboard',
            'totalObat'         => $totalObat,
            'totalStokGudang'   => $totalStokGudang,
            'totalStokRak'      => $totalStokRak,
            'obatKritisCount'   => $obatKritisCount,
            'rakKritisCount'    => $rakKritisCount,
            'batchEdCount'      => $batchEdCount,
            'penjualanChart'    => $penjualanChart,
            'transferHariIni'   => $transferHariIni,
            'omzetHariIni'      => $omzetHariIni,
            'labaHariIni'       => $labaHariIni,
            'omzetBulanIni'     => $omzetBulanIni,
            'labaBulanIni'      => $labaBulanIni,
            'notifikasiTerbaru' => $notifikasiTerbaru,
            'pesananAktif'      => $pesananAktif,
            'transferTerakhir'  => $transferTerakhir,
            'penjualanTerakhir' => $penjualanTerakhir,
        ]);
    }
}
