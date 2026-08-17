<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\ObatBatch;
use Illuminate\Http\Request;

class DisplayRakController extends Controller
{
    public function index(Request $request)
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

        if ($request->filled('status_rak')) {
            if ($request->status_rak === 'kritis') {
                // Stok rak ≤ min_stok_rak
                $query->whereRaw('(SELECT COALESCE(SUM(stok_rak),0) FROM obat_batch WHERE obat_batch.obat_id = obat.id) <= obat.min_stok_rak');
            } elseif ($request->status_rak === 'aman') {
                $query->whereRaw('(SELECT COALESCE(SUM(stok_rak),0) FROM obat_batch WHERE obat_batch.obat_id = obat.id) > obat.min_stok_rak');
            }
        }

        $obats = $query->orderBy('nama_obat')->paginate(20)->withQueryString();

        // Statistik ringkas
        $totalObat       = Obat::count();
        $obatKritis      = Obat::whereRaw('(SELECT COALESCE(SUM(stok_rak),0) FROM obat_batch WHERE obat_batch.obat_id = obat.id) <= obat.min_stok_rak')->count();
        $obatHabisRak    = Obat::whereRaw('(SELECT COALESCE(SUM(stok_rak),0) FROM obat_batch WHERE obat_batch.obat_id = obat.id) = 0')->count();

        return view('pages.display-rak.index', [
            'title'       => 'Display Rak Obat',
            'obats'       => $obats,
            'totalObat'   => $totalObat,
            'obatKritis'  => $obatKritis,
            'obatHabisRak' => $obatHabisRak,
        ]);
    }
}
