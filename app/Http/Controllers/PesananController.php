<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\ObatBatch;
use App\Models\Obat;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $obats    = Obat::with('supplier')->orderBy('nama_obat')->get();
        $obatJson = $obats->map(function ($o) {
            return [
                'id'            => $o->id,
                'kode'          => $o->kode_obat,
                'nama'          => $o->nama_obat,
                'satuan_beli'   => $o->satuan_beli,
                'supplier_id'   => $o->supplier_id,
                'supplier_nama' => $o->supplier ? $o->supplier->nama_supplier : 'Belum ditentukan',
            ];
        })->values()->toJson();

        return view('pages.pesanan.create', [
            'title'    => 'Buat Pesanan Baru',
            'obats'    => $obats,
            'obatJson' => $obatJson,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_pesan'        => 'required|date',
            'catatan'              => 'nullable|string|max:500',
            'items'                => 'required|array|min:1',
            'items.*.obat_id'      => 'required|exists:obat,id',
            'items.*.jumlah_pesan' => 'required|integer|min:1',
        ]);

        $obatIds = collect($validated['items'])->pluck('obat_id')->unique();
        $obats   = Obat::with('supplier')->whereIn('id', $obatIds)->get()->keyBy('id');

        // Pastikan setiap obat yang dipesan memiliki data supplier
        foreach ($validated['items'] as $item) {
            $obat = $obats[$item['obat_id']] ?? null;
            if (! $obat || ! $obat->supplier_id) {
                return back()->withInput()->withErrors([
                    'items' => "Obat '" . ($obat->nama_obat ?? 'Pilihan') . "' belum memiliki supplier terkait. Silakan atur supplier pada Data Obat terlebih dahulu.",
                ]);
            }
        }

        // Kelompokkan item berdasarkan supplier_id dari obat
        $groupedBySupplier = collect($validated['items'])->groupBy(function ($item) use ($obats) {
            return $obats[$item['obat_id']]->supplier_id;
        });

        $createdCount = 0;
        foreach ($groupedBySupplier as $supplierId => $items) {
            $kodePesanan = 'PO-' . date('Ymd') . '-' . str_pad(Pesanan::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

            $pesanan = Pesanan::create([
                'kode_pesanan'  => $kodePesanan,
                'supplier_id'   => $supplierId,
                'user_id'       => auth()->id(),
                'tanggal_pesan' => $validated['tanggal_pesan'],
                'status'        => 'draft',
                'catatan'       => $validated['catatan'] ?? 'Pesanan manual',
            ]);

            foreach ($items as $item) {
                $obat          = $obats[$item['obat_id']];
                $lastBatch     = $obat->batches()->latest('id')->first();
                $estimasiHarga = $lastBatch ? $lastBatch->harga_beli_satuan : 0;

                DetailPesanan::create([
                    'pesanan_id'     => $pesanan->id,
                    'obat_id'        => $item['obat_id'],
                    'jumlah_pesan'   => $item['jumlah_pesan'],
                    'estimasi_harga' => $estimasiHarga,
                ]);
            }

            $createdCount++;
        }

        $message = $createdCount > 1
            ? "Berhasil membuat {$createdCount} pesanan (dikelompokkan otomatis berdasarkan masing-masing supplier)."
            : "Pesanan berhasil dibuat.";

        return redirect()->route('pesanan.index')->with('success', $message);
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

    /**
     * Terima pesanan yang berstatus 'dikirim': auto-create obat_batch per item,
     * lalu ubah status pesanan menjadi 'selesai'.
     */
    public function terima(Request $request, Pesanan $pesanan)
    {
        if ($pesanan->status !== 'dikirim') {
            return back()->with('error', 'Pesanan harus berstatus "Dikirim" sebelum dapat diterima.');
        }

        $validated = $request->validate([
            'items'                          => 'required|array|min:1',
            'items.*.detail_id'              => 'required|exists:detail_pesanan,id',
            'items.*.jumlah_diterima'        => 'required|integer|min:1',
            'items.*.tanggal_kadaluwarsa'    => 'required|date|after:today',
            'items.*.harga_beli_satuan'      => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['items'] as $item) {
                $detail     = DetailPesanan::with('obat')->findOrFail($item['detail_id']);
                $nomorBatch = ObatBatch::generateNomorBatch($detail->obat_id);

                ObatBatch::create([
                    'obat_id'             => $detail->obat_id,
                    'supplier_id'         => $pesanan->supplier_id,
                    'nomor_batch'         => $nomorBatch,
                    'tanggal_masuk'       => now()->toDateString(),
                    'tanggal_kadaluwarsa' => $item['tanggal_kadaluwarsa'],
                    'stok_gudang'         => $item['jumlah_diterima'],
                    'stok_rak'            => 0,
                    'harga_beli_satuan'   => $item['harga_beli_satuan'],
                    'harga_beli'          => $item['harga_beli_satuan'] * $item['jumlah_diterima'],
                ]);
            }

            $pesanan->update(['status' => 'selesai']);

            DB::commit();

            $jumlahItem = count($validated['items']);
            return redirect()->route('pesanan.show', $pesanan)
                ->with('success', "Pesanan berhasil diterima! {$jumlahItem} item obat telah ditambahkan ke gudang.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
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
