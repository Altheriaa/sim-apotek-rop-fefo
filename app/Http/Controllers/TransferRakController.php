<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\TransferRak;
use App\Services\StokService;
use Illuminate\Http\Request;

class TransferRakController extends Controller
{
    public function __construct(private StokService $stokService) {}

    public function index(Request $request)
    {
        $query = TransferRak::with(['obat', 'obatBatch', 'user']);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->whereHas('obat', fn ($q) => $q->where('nama_obat', 'like', "%{$search}%"))
                  ->orWhereHas('obatBatch', fn ($q) => $q->where('nomor_batch', 'like', "%{$search}%"));
        }

        if ($request->filled('tanggal_dari')) {
            $query->where('tanggal_transfer', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->where('tanggal_transfer', '<=', $request->tanggal_sampai);
        }

        $transfers = $query->latest('tanggal_transfer')->latest('id')->paginate(15)->withQueryString();

        return view('pages.transfer-rak.index', [
            'title'     => 'Transfer Gudang → Rak',
            'transfers' => $transfers,
        ]);
    }

    public function create()
    {
        // Hanya tampilkan obat yang punya stok gudang > 0
        $obats = Obat::whereHas('batches', fn ($q) => $q->where('stok_gudang', '>', 0))
            ->with(['batches' => fn ($q) => $q->where('stok_gudang', '>', 0)->orderBy('tanggal_kadaluwarsa')])
            ->orderBy('nama_obat')
            ->get();

        return view('pages.transfer-rak.create', [
            'title' => 'Transfer Stok ke Rak',
            'obats' => $obats,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'obat_id'    => 'required|exists:obat,id',
            'jumlah'     => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:255',
        ]);

        try {
            $batchDipindahkan = $this->stokService->transferKeRak(
                obatId:     $validated['obat_id'],
                jumlah:     $validated['jumlah'],
                userId:     auth()->id(),
                keterangan: $validated['keterangan'] ?? null,
            );

            $obat     = Obat::find($validated['obat_id']);
            $ringkasan = collect($batchDipindahkan)->map(fn ($b) => "Batch {$b['nomor_batch']} ({$b['jumlah']} {$obat->satuan}, ED: {$b['ed']})")->implode(', ');

            return redirect()->route('transfer-rak.index')
                ->with('success', "Transfer berhasil! {$ringkasan}");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }
}
