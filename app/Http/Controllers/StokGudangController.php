<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\ObatBatch;
use Illuminate\Http\Request;

class StokGudangController extends Controller
{
    public function index(Request $request)
    {
        $query = ObatBatch::with(['obat', 'supplier'])
            ->where('stok_gudang', '>', 0); // Hanya batch yang masih ada di gudang

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nomor_batch', 'like', "%{$search}%")
                  ->orWhereHas('obat', fn ($oq) => $oq->where('nama_obat', 'like', "%{$search}%"))
                  ->orWhereHas('supplier', fn ($sq) => $sq->where('nama_supplier', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status_ed')) {
            if ($request->status_ed === 'expired') {
                $query->where('tanggal_kadaluwarsa', '<', now()->toDateString());
            } elseif ($request->status_ed === 'expiring') {
                $query->whereBetween('tanggal_kadaluwarsa', [now()->toDateString(), now()->addDays(30)->toDateString()]);
            } elseif ($request->status_ed === 'safe') {
                $query->where('tanggal_kadaluwarsa', '>', now()->addDays(30)->toDateString());
            }
        }

        // Urut FEFO: batch dengan ED terdekat paling atas
        $batches = $query->orderBy('tanggal_kadaluwarsa', 'asc')->paginate(15)->withQueryString();

        // Ringkasan stok
        $totalBatch      = ObatBatch::where('stok_gudang', '>', 0)->count();
        $expiredBatch    = ObatBatch::where('stok_gudang', '>', 0)->where('tanggal_kadaluwarsa', '<', now())->count();
        $expiringBatch   = ObatBatch::where('stok_gudang', '>', 0)
            ->whereBetween('tanggal_kadaluwarsa', [now(), now()->addDays(30)])->count();

        return view('pages.stok-gudang.index', [
            'title'        => 'Stok Gudang (FEFO)',
            'batches'      => $batches,
            'totalBatch'   => $totalBatch,
            'expiredBatch' => $expiredBatch,
            'expiringBatch' => $expiringBatch,
        ]);
    }
}
