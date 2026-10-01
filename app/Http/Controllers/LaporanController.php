<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\ObatBatch;
use App\Models\ObatKeluar;
use App\Models\Penjualan;
use App\Models\TransferRak;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    // Laporan Obat Masuk
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

    // Laporan Transfer ke Rak (Gudang → Display Rak)
    public function transferRak(Request $request)
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

        return view('pages.laporan.transfer-rak', [
            'title'     => 'Laporan Transfer ke Rak',
            'data'      => $data,
            'startDate' => $startDate,
            'endDate'   => $endDate,
        ]);
    }

    public function transferRakPdf(Request $request)
    {
        $query = TransferRak::with(['obat', 'obatBatch', 'user']);

        if ($request->filled('start_date')) $query->where('tanggal_transfer', '>=', $request->start_date);
        if ($request->filled('end_date'))   $query->where('tanggal_transfer', '<=', $request->end_date);

        $data = $query->latest('tanggal_transfer')->get();

        $totalTransfer  = $data->count();
        $totalSatuanBeli = (int) $data->sum('jumlah_keluar');
        $totalSatuanJual = (int) $data->sum('jumlah_masuk_rak');

        $logoPath   = public_path('images/Logo Apotek Tabah Farma.png');
        $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : null;

        $pdf = Pdf::loadView('pages.laporan.pdf.transfer-rak', [
            'data'            => $data,
            'tanggalDari'     => $request->start_date,
            'tanggalSampai'   => $request->end_date,
            'totalTransfer'   => $totalTransfer,
            'totalSatuanBeli' => $totalSatuanBeli,
            'totalSatuanJual' => $totalSatuanJual,
            'logoBase64'      => $logoBase64,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('laporan-transfer-rak-' . date('Ymd_His') . '.pdf');
    }

    // Laporan Disposal / Pembuangan Obat Expired & Rusak
    public function disposal(Request $request)
    {
        $query = ObatKeluar::with(['obat', 'obatBatch', 'user']);

        $startDate = $request->tanggal_dari ?? now()->startOfMonth()->toDateString();
        $endDate   = $request->tanggal_sampai ?? now()->toDateString();

        if ($request->filled('tanggal_dari')) {
            $query->where('tanggal_keluar', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->where('tanggal_keluar', '<=', $request->tanggal_sampai);
        }
        if ($request->filled('alasan')) {
            $query->where('alasan', $request->alasan);
        }
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereHas('obat', fn ($oq) => $oq->where('nama_obat', 'like', "%{$search}%"))
                  ->orWhereHas('obatBatch', fn ($bq) => $bq->where('nomor_batch', 'like', "%{$search}%"));
            });
        }

        $data = $query->latest('tanggal_keluar')->latest('id')->paginate(15)->withQueryString();

        // Ringkasan total untuk periode
        $semuaDisposal = ObatKeluar::with(['obat'])
            ->when($request->tanggal_dari, fn ($q) => $q->where('tanggal_keluar', '>=', $request->tanggal_dari))
            ->when($request->tanggal_sampai, fn ($q) => $q->where('tanggal_keluar', '<=', $request->tanggal_sampai))
            ->when($request->alasan, fn ($q) => $q->where('alasan', $request->alasan))
            ->get();

        $totalGudangDisposal = (int) $semuaDisposal->sum('jumlah_gudang');
        $totalRakDisposal    = (int) $semuaDisposal->sum('jumlah_rak');
        $totalRecord         = $semuaDisposal->count();
        $alasanOptions       = ObatKeluar::$alasanOptions;

        return view('pages.laporan.disposal', [
            'title'               => 'Laporan Disposal / Pembuangan Obat',
            'data'                => $data,
            'startDate'           => $startDate,
            'endDate'             => $endDate,
            'alasanOptions'       => $alasanOptions,
            'totalGudangDisposal' => $totalGudangDisposal,
            'totalRakDisposal'    => $totalRakDisposal,
            'totalRecord'         => $totalRecord,
        ]);
    }

    public function disposalPdf(Request $request)
    {
        $query = ObatKeluar::with(['obat', 'obatBatch', 'user']);

        $startDate = $request->start_date ?? $request->tanggal_dari;
        $endDate   = $request->end_date ?? $request->tanggal_sampai;

        if ($startDate) $query->where('tanggal_keluar', '>=', $startDate);
        if ($endDate)   $query->where('tanggal_keluar', '<=', $endDate);
        if ($request->filled('alasan')) $query->where('alasan', $request->alasan);

        $data = $query->latest('tanggal_keluar')->latest('id')->get();

        $totalRecord         = $data->count();
        $totalGudangDisposal = (int) $data->sum('jumlah_gudang');
        $totalRakDisposal    = (int) $data->sum('jumlah_rak');
        $alasanOptions       = ObatKeluar::$alasanOptions;

        $logoPath   = public_path('images/Logo Apotek Tabah Farma.png');
        $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : null;

        $pdf = Pdf::loadView('pages.laporan.pdf.disposal', [
            'data'                => $data,
            'tanggalDari'         => $startDate,
            'tanggalSampai'       => $endDate,
            'alasanFilter'        => $request->alasan,
            'alasanOptions'       => $alasanOptions,
            'totalRecord'         => $totalRecord,
            'totalGudangDisposal' => $totalGudangDisposal,
            'totalRakDisposal'    => $totalRakDisposal,
            'logoBase64'          => $logoBase64,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('laporan-disposal-' . date('Ymd_His') . '.pdf');
    }

    // Laporan Stok Obat (Gudang + rak)
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

        if ($request->filled('status_rop') && $request->status_rop === 'kritis') {
            // rop_dinamis dihitung di PHP, tidak bisa di SQL langsung.
            // Preload total penjualan 30 hari dalam satu query untuk menghindari N+1.
            $penjualanPerObat = \App\Models\DetailPenjualan::query()
                ->whereHas('penjualan', fn ($q) =>
                    $q->where('tanggal_transaksi', '>=', now()->subDays(30))
                )
                ->selectRaw('obat_id, SUM(jumlah) as total_terjual')
                ->groupBy('obat_id')
                ->pluck('total_terjual', 'obat_id');

            $allData = $query->orderBy('nama_obat')->get();
            $allData = $allData->filter(function ($obat) use ($penjualanPerObat) {
                $d = round(($penjualanPerObat[$obat->id] ?? 0) / 30, 2);
                $l = (int) ($obat->lead_time_hari ?? 3);
                $isiPerKemasan = max((int) $obat->isi_per_kemasan, 1);

                $ropDinamis = $d > 0
                    ? (int) ceil(($d * $l) / $isiPerKemasan)
                    : (int) $obat->rop_minimum;

                $stokGudang = (int) ($obat->stok_gudang_total ?? 0);
                $stokRak    = (int) ($obat->stok_rak_total ?? 0);
                $stokTotal  = ($stokGudang * $isiPerKemasan) + $stokRak;

                return $ropDinamis > 0 && $stokTotal <= ($ropDinamis * $isiPerKemasan);
            })->values();

            $page    = $request->get('page', 1);
            $perPage = 20;
            $data    = new \Illuminate\Pagination\LengthAwarePaginator(
                $allData->forPage($page, $perPage),
                $allData->count(),
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );

            return view('pages.laporan.stok-obat', [
                'title' => 'Laporan Stok Obat',
                'data'  => $data,
            ]);
        }

        $data = $query->orderBy('nama_obat')->paginate(20)->withQueryString();

        return view('pages.laporan.stok-obat', [
            'title' => 'Laporan Stok Obat',
            'data'  => $data,
        ]);
    }

   
    // Laporan Penjualan Kasir
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
