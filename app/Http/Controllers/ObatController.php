<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use Illuminate\Http\Request;

class ObatController extends Controller
{
    public function index(Request $request)
    {
        $query = Obat::query()->withSum('batches as total_stok_sisa', 'stok_sisa');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nama_obat', 'like', "%{$search}%")
                  ->orWhere('satuan', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'rop') {
                $query->havingRaw('COALESCE(total_stok_sisa, 0) <= obat.rop_minimum');
            } elseif ($request->status === 'aman') {
                $query->havingRaw('COALESCE(total_stok_sisa, 0) > obat.rop_minimum');
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
        return view('pages.obat.create', [
            'title' => 'Tambah Obat',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_obat'    => 'required|string|max:255',
            'satuan'       => 'required|string|max:50',
            'rop_minimum'  => 'required|integer|min:0',
        ]);

        Obat::create($validated);

        return redirect()->route('obat.index')
            ->with('success', 'Data obat berhasil ditambahkan.');
    }

    public function show(Obat $obat)
    {
        $obat->load(['batches' => function ($query) {
            $query->orderBy('tanggal_kadaluwarsa', 'asc');
        }, 'batches.supplier']);

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
            'nama_obat'    => 'required|string|max:255',
            'satuan'       => 'required|string|max:50',
            'rop_minimum'  => 'required|integer|min:0',
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
