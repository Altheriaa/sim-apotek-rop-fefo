<?php

namespace App\Http\Controllers;

use App\Models\ObatBatch;
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
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereHas('obat', fn ($oq) => $oq->where('nama_obat', 'like', "%{$search}%"))
                  ->orWhereHas('obatBatch', fn ($bq) => $bq->where('nomor_batch', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('alasan')) {
            $query->where('alasan', $request->alasan);
        }

        if ($request->filled('tanggal_dari')) {
            $query->where('tanggal_keluar', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->where('tanggal_keluar', '<=', $request->tanggal_sampai);
        }

        $disposals = $query->latest('tanggal_keluar')->latest('id')->paginate(15)->withQueryString();

        return view('pages.obat-keluar.index', [
            'title'    => 'Disposal / Pembuangan Obat',
            'disposals' => $disposals,
            'alasanOptions' => ObatKeluar::$alasanOptions,
        ]);
    }

    public function create(Request $request)
    {
        $batches = ObatBatch::with('obat')
            ->where(function ($q) {
                $q->where('stok_gudang', '>', 0)->orWhere('stok_rak', '>', 0);
            })
            ->orderByRaw('tanggal_kadaluwarsa <= CURDATE() DESC')
            ->orderBy('tanggal_kadaluwarsa', 'asc')
            ->get();

        return view('pages.obat-keluar.create', [
            'title'   => 'Catat Pembuangan Obat',
            'batches' => $batches,
            'alasanOptions' => ObatKeluar::$alasanOptions,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'obat_batch_id' => 'required|exists:obat_batch,id',
            'jumlah_gudang' => 'required|integer|min:0',
            'jumlah_rak'    => 'required|integer|min:0',
            'alasan'        => 'required|in:expired,rusak,lainnya',
            'catatan'       => 'nullable|string|max:500',
        ]);

        try {
            $disposal = $this->stokService->disposalBatch(
                batchId:      $validated['obat_batch_id'],
                jumlahGudang: $validated['jumlah_gudang'],
                jumlahRak:    $validated['jumlah_rak'],
                alasan:       $validated['alasan'],
                userId:       auth()->id(),
                catatan:      $validated['catatan'] ?? null,
            );

            $batch  = $disposal->obatBatch()->with('obat')->first();
            $label  = ObatKeluar::$alasanOptions[$validated['alasan']] ?? $validated['alasan'];

            return redirect()->route('obat-keluar.index')
                ->with('success', "Disposal berhasil dicatat: Batch {$batch->nomor_batch} ({$batch->obat->nama_obat}), alasan {$label}.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }
}
