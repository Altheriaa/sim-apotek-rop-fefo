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

        $startDate = $request->start_date ?? $request->tanggal_dari;
        $endDate   = $request->end_date ?? $request->tanggal_sampai;

        if ($startDate) {
            $query->where('tanggal_masuk', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('tanggal_masuk', '<=', $endDate);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nomor_batch', 'like', "%{$search}%")
                  ->orWhereHas('obat', fn ($oq) => $oq->where('nama_obat', 'like', "%{$search}%"))
                  ->orWhereHas('supplier', fn ($sq) => $sq->where('nama_supplier', 'like', "%{$search}%"));
            });
        }

        $data = $query->orderBy('tanggal_masuk', 'desc')->get();

        $totalBatch     = $data->count();
        $totalNilaiBeli = (float) $data->sum(function ($b) {
            if ($b->harga_beli && $b->harga_beli > 0) {
                return (float) $b->harga_beli;
            }
            return (float) ($b->stok_gudang * ($b->harga_beli_satuan ?? 0));
        });
        $totalSupplier  = $data->pluck('supplier_id')->filter()->unique()->count();

        $logoPath = public_path('images/Logo Apotek Tabah Farma.png');
        $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : null;

        $pdf = Pdf::loadView('pages.laporan.pdf.obat-masuk', [
            'data'           => $data,
            'tanggalDari'    => $startDate,
            'tanggalSampai'  => $endDate,
            'search'         => $request->search,
            'totalBatch'     => $totalBatch,
            'totalNilaiBeli' => $totalNilaiBeli,
            'totalSupplier'  => $totalSupplier,
            'logoBase64'     => $logoBase64,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('laporan-obat-masuk-' . date('Ymd_His') . '.pdf');
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
                $query->whereRaw('(
                    (SELECT COALESCE(SUM(stok_gudang),0) FROM obat_batch WHERE obat_batch.obat_id = obat.id) * obat.isi_per_kemasan
                    + (SELECT COALESCE(SUM(stok_rak),0) FROM obat_batch WHERE obat_batch.obat_id = obat.id)
                ) <= (obat.rop_minimum * obat.isi_per_kemasan)');
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
        $query = Penjualan::with(['user', 'details.obat', 'details.obatBatch']);

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

        $data = $query->latest('tanggal_transaksi')->paginate(15)->withQueryString();

        // Hitung akumulasi omzet, HPP modal, dan laba untuk periode filter
        $semuaTrx = Penjualan::with(['details.obat', 'details.obatBatch'])
            ->when($request->tanggal_dari, fn ($q) => $q->whereDate('tanggal_transaksi', '>=', $request->tanggal_dari))
            ->when($request->tanggal_sampai, fn ($q) => $q->whereDate('tanggal_transaksi', '<=', $request->tanggal_sampai))
            ->get();

        $totalPendapatan = (float) $semuaTrx->sum('total_harga');
        $totalHpp        = (float) $semuaTrx->sum(fn ($trx) => $trx->total_hpp);
        $totalLaba       = (float) ($totalPendapatan - $totalHpp);

        return view('pages.laporan.penjualan', [
            'title'           => 'Laporan Penjualan',
            'data'            => $data,
            'startDate'       => $startDate,
            'endDate'         => $endDate,
            'totalPendapatan' => $totalPendapatan,
            'totalHpp'        => $totalHpp,
            'totalLaba'       => $totalLaba,
        ]);
    }

    public function penjualanPdf(Request $request)
    {
        $query = Penjualan::with(['user', 'details.obat', 'details.obatBatch']);

        $startDate = $request->start_date ?? $request->tanggal_dari;
        $endDate   = $request->end_date ?? $request->tanggal_sampai;

        if ($startDate) {
            $query->whereDate('tanggal_transaksi', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('tanggal_transaksi', '<=', $endDate);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('no_transaksi', 'like', "%{$search}%")
                  ->orWhere('nama_pembeli', 'like', "%{$search}%")
                  ->orWhereHas('user', fn ($uq) => $uq->where('nama_user', 'like', "%{$search}%"));
            });
        }

        $data = $query->latest('tanggal_transaksi')->get();

        $totalTransaksi  = $data->count();
        $totalPendapatan = (float) $data->sum('total_harga');
        $totalHpp        = (float) $data->sum(fn ($trx) => $trx->total_hpp);
        $totalLaba       = (float) ($totalPendapatan - $totalHpp);
        $marginPersen    = $totalPendapatan > 0 ? round(($totalLaba / $totalPendapatan) * 100, 1) : 0;

        $logoPath = public_path('images/Logo Apotek Tabah Farma.png');
        $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : null;

        $pdf = Pdf::loadView('pages.laporan.pdf.penjualan', [
            'data'            => $data,
            'tanggalDari'     => $startDate,
            'tanggalSampai'   => $endDate,
            'search'          => $request->search,
            'totalTransaksi'  => $totalTransaksi,
            'totalPendapatan' => $totalPendapatan,
            'totalHpp'        => $totalHpp,
            'totalLaba'       => $totalLaba,
            'marginPersen'    => $marginPersen,
            'logoBase64'      => $logoBase64,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('laporan-penjualan-' . date('Ymd_His') . '.pdf');
    }
}
