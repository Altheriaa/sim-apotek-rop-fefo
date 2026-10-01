<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\Supplier;
use Illuminate\Http\Request;

class ObatController extends Controller
{
    public function index(Request $request)
    {
        // Gabung stok_gudang + stok_rak sebagai total stok
        $query = Obat::query()
            ->withSum('batches as total_stok_gudang', 'stok_gudang')
            ->withSum('batches as total_stok_rak', 'stok_rak');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nama_obat', 'like', "%{$search}%")
                  ->orWhere('kode_obat', 'like', "%{$search}%")
                  ->orWhere('kategori', 'like', "%{$search}%")
                  ->orWhere('satuan_jual', 'like', "%{$search}%")
                  ->orWhere('satuan_beli', 'like', "%{$search}%");
            });
        }

        // Filter status ROP menggunakan rop_dinamis (dihitung di PHP via accessor)
        // rop_dinamis = D×L fallback ke rop_minimum jika belum ada riwayat penjualan.
        $filterStatus = $request->status;

        if ($filterStatus === 'rop' || $filterStatus === 'aman') {
            $allObats = $query->latest('id')->get();
            $allObats = $allObats->filter(function ($obat) use ($filterStatus) {
                $ropDinamis = $obat->rop_dinamis;
                $totalSatuanJual = ($obat->total_stok_gudang * $obat->isi_per_kemasan) + $obat->total_stok_rak;
                $batas = $ropDinamis * $obat->isi_per_kemasan;
                return $filterStatus === 'rop'
                    ? ($ropDinamis > 0 && $totalSatuanJual <= $batas)
                    : ($ropDinamis <= 0 || $totalSatuanJual > $batas);
            })->values();

            return view('pages.obat.index', [
                'title' => 'Data Obat',
                'obats' => new \Illuminate\Pagination\LengthAwarePaginator(
                    $allObats->forPage(\Illuminate\Support\Facades\Request::get('page', 1), 10),
                    $allObats->count(),
                    10,
                    null,
                    ['path' => \Illuminate\Support\Facades\Request::url(), 'query' => request()->query()]
                ),
            ]);
        }

        $obats = $query->latest('id')->paginate(10)->withQueryString();

        return view('pages.obat.index', [
            'title' => 'Data Obat',
            'obats' => $obats,
        ]);
    }

    public function create()
    {
        $kodeOtomatis = Obat::generateKodeObat();
        $suppliers = Supplier::all();

        return view('pages.obat.create', [
            'title' => 'Tambah Obat',
            'kodeOtomatis' => $kodeOtomatis,
            'suppliers' => $suppliers,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_obat'       => 'nullable|string|max:50|unique:obat,kode_obat',
            'supplier_id'     => 'required|exists:supplier,id',
            'nama_obat'       => 'required|string|max:255',
            'kategori'        => 'nullable|string|max:100',
            'satuan_beli'     => 'required|string|max:50',
            'satuan_jual'     => 'required|string|max:50',
            'isi_per_kemasan' => 'required|integer|min:1',
            'harga_jual'      => 'required|numeric|min:0',
            'rop_minimum'     => 'required|integer|min:0',
            'lead_time_hari'  => 'required|integer|min:1|max:365',
            'min_stok_rak'    => 'required|integer|min:0',
        ]);

        if (empty($validated['kode_obat'])) {
            $validated['kode_obat'] = Obat::generateKodeObat();
        }

        Obat::create($validated);

        return redirect()->route('obat.index')
            ->with('success', 'Data obat berhasil ditambahkan.');
    }

    public function show(Obat $obat)
    {
        $obat->load([
            'batches' => function ($query) {
                $query->orderBy('tanggal_kadaluwarsa', 'asc');
            },
            'batches.supplier',
        ]);

        return view('pages.obat.show', [
            'title' => 'Detail Obat: ' . $obat->nama_obat,
            'obat'  => $obat,
        ]);
    }

    public function edit(Obat $obat)
    {
        $suppliers = Supplier::all();

        return view('pages.obat.edit', [
            'title' => 'Edit Obat',
            'obat'  => $obat,
            'suppliers' => $suppliers,
        ]);
    }

    public function update(Request $request, Obat $obat)
    {
        $validated = $request->validate([
            'kode_obat'       => 'nullable|string|max:50|unique:obat,kode_obat,' . $obat->id,
            'supplier_id'     => 'required|exists:supplier,id',
            'nama_obat'       => 'required|string|max:255',
            'kategori'        => 'nullable|string|max:100',
            'satuan_beli'     => 'required|string|max:50',
            'satuan_jual'     => 'required|string|max:50',
            'isi_per_kemasan' => 'required|integer|min:1',
            'harga_jual'      => 'required|numeric|min:0',
            'rop_minimum'     => 'required|integer|min:0',
            'lead_time_hari'  => 'required|integer|min:1|max:365',
            'min_stok_rak'    => 'required|integer|min:0',
        ]);

        $obat->update($validated);

        return redirect()->route('obat.index')
            ->with('success', 'Data obat berhasil diperbarui.');
    }

    public function destroy(Obat $obat)
    {
        $obat->delete();

        return redirect()->route('obat.index')
            ->with('success', 'Data obat berhasil dihapus.');
    }
}
