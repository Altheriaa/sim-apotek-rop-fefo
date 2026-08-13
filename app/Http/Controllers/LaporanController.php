<?php

namespace App\Http\Controllers;

use App\Models\ObatBatch;
use App\Models\ObatKeluar;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    public function obatMasuk(Request $request)
    {
        $query = ObatBatch::with(['obat', 'supplier']);

        $startDate = $request->tanggal_dari ?? now()->startOfMonth()->toDateString();
        $endDate = $request->tanggal_sampai ?? now()->toDateString();

        if ($request->filled('tanggal_dari')) {
            $query->where('tanggal_masuk', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->where('tanggal_masuk', '<=', $request->tanggal_sampai);
        }

        if ($request->filled('obat_search')) {
            $query->whereHas('obat', function ($q) use ($request) {
                $q->where('nama_obat', 'like', '%' . $request->obat_search . '%');
            });
        }

        $data = $query->orderBy('tanggal_masuk', 'desc')->get();

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

        if ($request->filled('start_date')) {
            $query->where('tanggal_masuk', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('tanggal_masuk', '<=', $request->end_date);
        }

        $data = $query->orderBy('tanggal_masuk', 'desc')->get();

        $pdf = Pdf::loadView('pages.laporan.pdf.obat-masuk', [
            'data'          => $data,
            'tanggalDari'   => $request->start_date,
            'tanggalSampai' => $request->end_date,
        ]);

        return $pdf->download('laporan-obat-masuk.pdf');
    }

    public function obatKeluar(Request $request)
    {
        $query = ObatKeluar::with(['obat', 'obatBatch', 'user']);

        $startDate = $request->tanggal_dari ?? now()->startOfMonth()->toDateString();
        $endDate = $request->tanggal_sampai ?? now()->toDateString();

        if ($request->filled('tanggal_dari')) {
            $query->where('tanggal_keluar', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->where('tanggal_keluar', '<=', $request->tanggal_sampai);
        }

        if ($request->filled('obat_search')) {
            $query->whereHas('obat', function ($q) use ($request) {
                $q->where('nama_obat', 'like', '%' . $request->obat_search . '%');
            });
        }

        $data = $query->orderBy('tanggal_keluar', 'desc')->get();

        return view('pages.laporan.obat-keluar', [
            'title'     => 'Laporan Obat Keluar',
            'data'      => $data,
            'startDate' => $startDate,
            'endDate'   => $endDate,
        ]);
    }

    public function obatKeluarPdf(Request $request)
    {
        $query = ObatKeluar::with(['obat', 'obatBatch', 'user']);

        if ($request->filled('start_date')) {
            $query->where('tanggal_keluar', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('tanggal_keluar', '<=', $request->end_date);
        }

        $data = $query->orderBy('tanggal_keluar', 'desc')->get();

        $pdf = Pdf::loadView('pages.laporan.pdf.obat-keluar', [
            'data'          => $data,
            'tanggalDari'   => $request->start_date,
            'tanggalSampai' => $request->end_date,
        ]);

        return $pdf->download('laporan-obat-keluar.pdf');
    }
}
