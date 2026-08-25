<?php

namespace App\Http\Controllers;

use App\Models\Obat;
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

        if ($request->filled('status')) {
            if ($request->status === 'rop') {
                // Total stok apotek (satuan_jual) ≤ Batas ROP (rop_minimum dalam satuan_beli × isi_per_kemasan)
                $query->whereRaw('(
                    (SELECT COALESCE(SUM(stok_gudang),0) FROM obat_batch WHERE obat_batch.obat_id = obat.id) * obat.isi_per_kemasan
                    + (SELECT COALESCE(SUM(stok_rak),0) FROM obat_batch WHERE obat_batch.obat_id = obat.id)
                ) <= (obat.rop_minimum * obat.isi_per_kemasan)');
            } elseif ($request->status === 'aman') {
                $query->whereRaw('(
                    (SELECT COALESCE(SUM(stok_gudang),0) FROM obat_batch WHERE obat_batch.obat_id = obat.id) * obat.isi_per_kemasan
                    + (SELECT COALESCE(SUM(stok_rak),0) FROM obat_batch WHERE obat_batch.obat_id = obat.id)
                ) > (obat.rop_minimum * obat.isi_per_kemasan)');
            }
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

        return view('pages.obat.create', [
            'title'        => 'Tambah Obat',
            'kodeOtomatis' => $kodeOtomatis,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_obat'       => 'nullable|string|max:50|unique:obat,kode_obat',
            'nama_obat'       => 'required|string|max:255',
            'kategori'        => 'nullable|string|max:100',
            'satuan_beli'     => 'required|string|max:50',
            'satuan_jual'     => 'required|string|max:50',
            'isi_per_kemasan' => 'required|integer|min:1',
            'harga_jual'      => 'required|numeric|min:0',
            'rop_minimum'     => 'required|integer|min:0',
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
        return view('pages.obat.edit', [
            'title' => 'Edit Obat',
            'obat'  => $obat,
        ]);
    }

    public function update(Request $request, Obat $obat)
    {
        $validated = $request->validate([
            'kode_obat'       => 'nullable|string|max:50|unique:obat,kode_obat,' . $obat->id,
            'nama_obat'       => 'required|string|max:255',
            'kategori'        => 'nullable|string|max:100',
            'satuan_beli'     => 'required|string|max:50',
            'satuan_jual'     => 'required|string|max:50',
            'isi_per_kemasan' => 'required|integer|min:1',
            'harga_jual'      => 'required|numeric|min:0',
            'rop_minimum'     => 'required|integer|min:0',
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
