<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\Obat;
use App\Models\Supplier;
use Illuminate\Http\Request;

class PesananController extends Controller
{
    public function index(Request $request)
    {
        $query = Pesanan::with(['supplier', 'user', 'detailPesanan.obat']);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $cleanId = ltrim($search, '#');
            $query->where(function ($q) use ($search, $cleanId) {
                if (is_numeric($cleanId)) {
                    $q->orWhere('id', $cleanId);
                }
                $q->orWhere('kode_pesanan', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function ($sq) use ($search) {
                      $sq->where('nama_supplier', 'like', "%{$search}%");
                  })->orWhereHas('detailPesanan.obat', function ($oq) use ($search) {
                      $oq->where('nama_obat', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tanggal_dari')) {
            $query->where('tanggal_pesan', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->where('tanggal_pesan', '<=', $request->tanggal_sampai);
        }

        $pesanans = $query->latest('tanggal_pesan')->latest('id')->paginate(10)->withQueryString();

        return view('pages.pesanan.index', [
            'title'    => 'Data Pesanan',
            'pesanans' => $pesanans,
        ]);
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('nama_supplier')->get();
        $obats     = Obat::orderBy('nama_obat')->get();

        return view('pages.pesanan.create', [
            'title'     => 'Buat Pesanan Baru',
            'suppliers' => $suppliers,
            'obats'     => $obats,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id'         => 'required|exists:supplier,id',
            'tanggal_pesan'       => 'required|date',
            'items'               => 'required|array|min:1',
            'items.*.obat_id'     => 'required|exists:obat,id',
            'items.*.jumlah_pesan' => 'required|integer|min:1',
        ]);

        $kodePesanan = 'PO-' . date('Ymd') . '-' . str_pad(Pesanan::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

        $pesanan = Pesanan::create([
            'kode_pesanan'  => $kodePesanan,
            'supplier_id'   => $validated['supplier_id'],
            'user_id'       => auth()->id(),
            'tanggal_pesan' => $validated['tanggal_pesan'],
            'status'        => 'draft',
        ]);

        foreach ($validated['items'] as $item) {
            DetailPesanan::create([
                'pesanan_id'  => $pesanan->id,
                'obat_id'     => $item['obat_id'],
                'jumlah_pesan' => $item['jumlah_pesan'],
            ]);
        }

        return redirect()->route('pesanan.index')
            ->with('success', 'Pesanan berhasil dibuat.');
    }

    public function show(Pesanan $pesanan)
    {
        $pesanan->load(['supplier', 'user', 'detailPesanan.obat']);

        return view('pages.pesanan.show', [
            'title'   => 'Detail Pesanan #' . $pesanan->id,
            'pesanan' => $pesanan,
        ]);
    }

    /**
     * Update status pesanan (draft → diproses → dikirim → selesai)
     */
    public function updateStatus(Request $request, Pesanan $pesanan)
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,diproses,dikirim,selesai',
        ]);

        $pesanan->update(['status' => $validated['status']]);

        $statusLabel = ucfirst($validated['status']);

        return redirect()->route('pesanan.show', $pesanan)
            ->with('success', "Status pesanan berubah menjadi: {$statusLabel}.");
    }

    public function destroy(Pesanan $pesanan)
    {
        if (!$pesanan->isDraft()) {
            return back()->with('error', 'Hanya pesanan berstatus draft yang bisa dihapus.');
        }

        $pesanan->delete();

        return redirect()->route('pesanan.index')
            ->with('success', 'Pesanan berhasil dihapus.');
    }
}
