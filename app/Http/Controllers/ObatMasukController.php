<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\ObatBatch;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ObatMasukController extends Controller
{
    public function index(Request $request)
    {
        $query = ObatBatch::with(['obat', 'supplier']);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nomor_batch', 'like', "%{$search}%")
                  ->orWhereHas('obat', function ($oq) use ($search) {
                      $oq->where('nama_obat', 'like', "%{$search}%");
                  })
                  ->orWhereHas('supplier', function ($sq) use ($search) {
                      $sq->where('nama_supplier', 'like', "%{$search}%");
                  });
            });
        }

        $tanggalDari   = $request->tanggal_dari;
        $tanggalSampai = $request->tanggal_sampai;

        if ($tanggalDari) {
            $query->where('tanggal_masuk', '>=', $tanggalDari);
        }

        if ($tanggalSampai) {
            $query->where('tanggal_masuk', '<=', $tanggalSampai);
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

        $batches = $query->latest('tanggal_masuk')->latest('id')->paginate(10)->withQueryString();

        return view('pages.obat-masuk.index', [
            'title'   => 'Obat Masuk (Gudang)',
            'batches' => $batches,
        ]);
    }

    public function create()
    {
        $obats     = Obat::orderBy('nama_obat')->get();
        $suppliers = Supplier::orderBy('nama_supplier')->get();

        return view('pages.obat-masuk.create', [
            'title'     => 'Tambah Obat Masuk',
            'obats'     => $obats,
            'suppliers' => $suppliers,
        ]);
    }

    public function generateBatchNumber(Request $request)
    {
        $obatId = $request->query('obat_id');
        $nomorBatch = ObatBatch::generateNomorBatch($obatId ? (int) $obatId : null);

        return response()->json([
            'nomor_batch' => $nomorBatch,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'obat_id'             => 'required|exists:obat,id',
            'supplier_id'         => 'required|exists:supplier,id',
            'nomor_batch'         => 'nullable|string|max:100',
            'tanggal_kadaluwarsa' => 'required|date|after:today',
            'jumlah'              => 'required|integer|min:1',
            'harga_beli'          => 'required|numeric|min:0',
        ]);

        if (empty($validated['nomor_batch'])) {
            $validated['nomor_batch'] = ObatBatch::generateNomorBatch((int) $validated['obat_id']);
        }

        DB::beginTransaction();
        try {
            ObatBatch::create([
                'obat_id'             => $validated['obat_id'],
                'supplier_id'         => $validated['supplier_id'],
                'nomor_batch'         => $validated['nomor_batch'],
                'tanggal_masuk'       => now()->toDateString(),
                'tanggal_kadaluwarsa' => $validated['tanggal_kadaluwarsa'],
                'stok_awal'           => $validated['jumlah'],
                'stok_gudang'         => $validated['jumlah'],  // Masuk ke GUDANG
                'stok_rak'            => 0,                     // Rak mulai dari 0
                'harga_beli'          => $validated['harga_beli'],
            ]);

            DB::commit();

            return redirect()->route('obat-masuk.index')
                ->with('success', "Obat masuk berhasil dicatat. Stok masuk ke gudang sebanyak {$validated['jumlah']} {$validated['nomor_batch']}.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $batch = ObatBatch::with(['obat', 'supplier'])->findOrFail($id);

        return view('pages.obat-masuk.show', [
            'title' => 'Detail Obat Masuk',
            'batch' => $batch,
        ]);
    }
}
