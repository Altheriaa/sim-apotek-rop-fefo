<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query();

        if ($request->filled('search')) {
            $query->where('nama_supplier', 'like', '%' . $request->search . '%');
        }

        $suppliers = $query->latest()->paginate(15);

        return view('pages.supplier.index', [
            'title'     => 'Data Supplier',
            'suppliers' => $suppliers,
        ]);
    }

    public function create()
    {
        return view('pages.supplier.create', [
            'title' => 'Tambah Supplier',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_supplier' => 'required|string|max:255',
            'alamat'        => 'nullable|string',
            'kontak'        => 'nullable|string|max:100',
        ]);

        Supplier::create($validated);

        return redirect()->route('supplier.index')
            ->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function edit(Supplier $supplier)
    {
        return view('pages.supplier.edit', [
            'title'    => 'Edit Supplier',
            'supplier' => $supplier,
        ]);
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'nama_supplier' => 'required|string|max:255',
            'alamat'        => 'nullable|string',
            'kontak'        => 'nullable|string|max:100',
        ]);

        $supplier->update($validated);

        return redirect()->route('supplier.index')
            ->with('success', 'Supplier berhasil diperbarui.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->route('supplier.index')
            ->with('success', 'Supplier berhasil dihapus.');
    }
}
