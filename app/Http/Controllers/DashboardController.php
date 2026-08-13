<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\ObatBatch;
use App\Models\ObatKeluar;
use App\Models\Notifikasi;
use App\Models\Pesanan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistik utama
        $totalObat      = Obat::count();
        $totalStok       = (int) ObatBatch::sum('stok_sisa');
        $obatKritisCount = 0;
        $batchEdCount    = ObatBatch::where('stok_sisa', '>', 0)
            ->where('tanggal_kadaluwarsa', '<=', now()->addDays(30))
            ->count();

        // Hitung obat dengan stok di bawah ROP
        $obatList = Obat::where('rop_minimum', '>', 0)->get();
        foreach ($obatList as $obat) {
            if ($obat->stok_total <= $obat->rop_minimum) {
                $obatKritisCount++;
            }
        }

        // Obat keluar 7 hari terakhir (untuk chart)
        $obatKeluarChart = ObatKeluar::selectRaw('DATE(tanggal_keluar) as tanggal, SUM(jumlah) as total')
            ->where('tanggal_keluar', '>=', now()->subDays(7))
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        // Notifikasi terbaru
        $notifikasiTerbaru = Notifikasi::with('obat')
            ->latest()
            ->take(5)
            ->get();

        // Pesanan aktif (bukan selesai)
        $pesananAktif = Pesanan::with('supplier')
            ->whereIn('status', ['draft', 'diproses', 'dikirim'])
            ->latest()
            ->take(5)
            ->get();

        // Aktivitas terakhir (obat keluar)
        $aktivitasTerakhir = ObatKeluar::with(['obat', 'user', 'obatBatch'])
            ->latest()
            ->take(10)
            ->get();

        return view('pages.dashboard', [
            'title'             => 'Dashboard',
            'totalObat'         => $totalObat,
            'totalStok'         => $totalStok,
            'obatKritisCount'   => $obatKritisCount,
            'batchEdCount'      => $batchEdCount,
            'obatKeluarChart'   => $obatKeluarChart,
            'notifikasiTerbaru' => $notifikasiTerbaru,
            'pesananAktif'      => $pesananAktif,
            'aktivitasTerakhir' => $aktivitasTerakhir,
        ]);
    }
}
