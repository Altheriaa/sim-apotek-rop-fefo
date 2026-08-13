<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\ObatKeluar;
use App\Services\StokService;
use Illuminate\Http\Request;

class ObatKeluarController extends Controller
{
    public function __construct(
        protected StokService $stokService
    ) {}

    public function index(Request $request)
    {
        $query = ObatKeluar::with(['obat', 'obatBatch', 'user']);

        if ($request->filled('search')) {
            $query->whereHas('obat', function ($q) use ($request) {
                $q->where('nama_obat', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('tanggal_dari')) {
            $query->where('tanggal_keluar', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->where('tanggal_keluar', '<=', $request->tanggal_sampai);
        }

        $obatKeluars = $query->latest()->paginate(15);

        return view('pages.obat-keluar.index', [
            'title'       => 'Obat Keluar',
            'obatKeluars' => $obatKeluars,
        ]);
    }

    public function create()
    {
        $obats = Obat::orderBy('nama_obat')->get();

        return view('pages.obat-keluar.create', [
            'title' => 'Tambah Obat Keluar',
            'obats' => $obats,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'obat_id' => 'required|exists:obat,id',
            'jumlah'  => 'required|integer|min:1',
        ]);

        try {
            $batchTerpakai = $this->stokService->keluarkanObat(
                $validated['obat_id'],
                $validated['jumlah'],
                auth()->id()
            );

            $detail = collect($batchTerpakai)->map(function ($b) {
                return "Batch {$b['no_batch']}: {$b['jumlah']} (ED: {$b['ed']})";
            })->join(', ');

            return redirect()->route('obat-keluar.index')
                ->with('success', "Obat keluar berhasil dicatat. Batch: {$detail}");
        } catch (\Exception $e) {
            return back()->withErrors(['jumlah' => $e->getMessage()])->withInput();
        }
    }
}
