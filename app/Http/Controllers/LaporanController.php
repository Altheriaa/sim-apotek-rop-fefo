<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\ObatBatch;
use App\Models\Penjualan;
use App\Models\TransferRak;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    // ══════════════════════════════════════════════════
    // Laporan Obat Masuk (Batch Gudang)
    // ══════════════════════════════════════════════════

    public function obatMasuk(Request $request)
    {
        $query = ObatBatch::with(['obat', 'supplier']);

        $startDate = $request->tanggal_dari ?? now()->startOfMonth()->toDateString();
        $endDate   = $request->tanggal_sampai ?? now()->toDateString();

        if ($request->filled('tanggal_dari')) {
            $query->where('tanggal_masuk', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->where('tanggal_masuk', '<=', $request->tanggal_sampai);
        }
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nomor_batch', 'like', "%{$search}%")
                  ->orWhereHas('obat', fn ($oq) => $oq->where('nama_obat', 'like', "%{$search}%"))
                  ->orWhereHas('supplier', fn ($sq) => $sq->where('nama_supplier', 'like', "%{$search}%"));
            });
        }

        $data = $query->orderBy('tanggal_masuk', 'desc')->paginate(15)->withQueryString();

        return view('pages.laporan.obat-masuk', [
            'title'     => 'Laporan Obat Masuk',
            'data'      => $data,
            'startDate' => $startDate,
            'endDate'   => $endDate,
        ]);
    }

    public function obatMasukPdf(Request $request)
    {
        $query = ObatBatch::with(['obat', 'supplier']);

        if ($request->filled('start_date')) $query->where('tanggal_masuk', '>=', $request->start_date);
        if ($request->filled('end_date'))   $query->where('tanggal_masuk', '<=', $request->end_date);

        $data = $query->orderBy('tanggal_masuk', 'desc')->get();

        $pdf = Pdf::loadView('pages.laporan.pdf.obat-masuk', [
            'data'          => $data,
            'tanggalDari'   => $request->start_date,
            'tanggalSampai' => $request->end_date,
        ]);

        return $pdf->download('laporan-obat-masuk.pdf');
    }

    // ══════════════════════════════════════════════════
    // Laporan Transfer ke Rak (menggantikan laporan obat-keluar)
    // ══════════════════════════════════════════════════

    public function obatKeluar(Request $request)
    {
        $query = TransferRak::with(['obat', 'obatBatch', 'user']);

        $startDate = $request->tanggal_dari ?? now()->startOfMonth()->toDateString();
        $endDate   = $request->tanggal_sampai ?? now()->toDateString();

        if ($request->filled('tanggal_dari')) {
            $query->where('tanggal_transfer', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->where('tanggal_transfer', '<=', $request->tanggal_sampai);
        }
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereHas('obat', fn ($oq) => $oq->where('nama_obat', 'like', "%{$search}%"))
                  ->orWhereHas('obatBatch', fn ($bq) => $bq->where('nomor_batch', 'like', "%{$search}%"))
                  ->orWhereHas('user', fn ($uq) => $uq->where('nama_user', 'like', "%{$search}%"));
            });
        }

        $data = $query->latest('tanggal_transfer')->paginate(15)->withQueryString();

        return view('pages.laporan.obat-keluar', [
            'title'     => 'Laporan Transfer ke Rak',
            'data'      => $data,
            'startDate' => $startDate,
            'endDate'   => $endDate,
        ]);
    }

    public function obatKeluarPdf(Request $request)
    {
        $query = TransferRak::with(['obat', 'obatBatch', 'user']);

        if ($request->filled('start_date')) $query->where('tanggal_transfer', '>=', $request->start_date);
        if ($request->filled('end_date'))   $query->where('tanggal_transfer', '<=', $request->end_date);

        $data = $query->latest('tanggal_transfer')->get();

        $pdf = Pdf::loadView('pages.laporan.pdf.obat-keluar', [
            'data'          => $data,
            'tanggalDari'   => $request->start_date,
            'tanggalSampai' => $request->end_date,
        ]);

        return $pdf->download('laporan-transfer-rak.pdf');
    }

    // ══════════════════════════════════════════════════
    // Laporan Stok Obat (Ringkasan Gudang + Rak)
    // ══════════════════════════════════════════════════

    public function stokObat(Request $request)
    {
        $query = Obat::withSum('batches as stok_gudang_total', 'stok_gudang')
            ->withSum('batches as stok_rak_total', 'stok_rak');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nama_obat', 'like', "%{$search}%")
                  ->orWhere('kode_obat', 'like', "%{$search}%")
                  ->orWhere('kategori', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status_rop')) {
            if ($request->status_rop === 'kritis') {
                $query->whereRaw('(SELECT COALESCE(SUM(stok_gudang)+SUM(stok_rak),0) FROM obat_batch WHERE obat_batch.obat_id = obat.id) <= obat.rop_minimum');
            }
        }

        $data = $query->orderBy('nama_obat')->paginate(20)->withQueryString();

        return view('pages.laporan.stok-obat', [
            'title' => 'Laporan Stok Obat',
            'data'  => $data,
        ]);
    }

    // ══════════════════════════════════════════════════
    // Laporan Penjualan Kasir
    // ══════════════════════════════════════════════════

    public function penjualan(Request $request)
    {
        $query = Penjualan::with(['user', 'details.obat']);

        $startDate = $request->tanggal_dari ?? now()->startOfMonth()->toDateString();
        $endDate   = $request->tanggal_sampai ?? now()->toDateString();

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_transaksi', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_transaksi', '<=', $request->tanggal_sampai);
        }
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('no_transaksi', 'like', "%{$search}%")
                  ->orWhere('nama_pembeli', 'like', "%{$search}%");
            });
        }

        $data            = $query->latest('tanggal_transaksi')->paginate(15)->withQueryString();
        $totalPendapatan = Penjualan::when($request->tanggal_dari, fn ($q) => $q->whereDate('tanggal_transaksi', '>=', $request->tanggal_dari))
            ->when($request->tanggal_sampai, fn ($q) => $q->whereDate('tanggal_transaksi', '<=', $request->tanggal_sampai))
            ->sum('total_harga');

        return view('pages.laporan.penjualan', [
            'title'           => 'Laporan Penjualan',
            'data'            => $data,
            'startDate'       => $startDate,
            'endDate'         => $endDate,
            'totalPendapatan' => $totalPendapatan,
        ]);
    }
}
