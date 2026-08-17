<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\Penjualan;
use App\Services\StokService;
use Illuminate\Http\Request;

class KasirController extends Controller
{
    public function __construct(private StokService $stokService) {}

    /**
     * Halaman POS Kasir — tampilkan daftar obat yang ada di rak
     */
    public function index(Request $request)
    {
        // Hanya tampilkan obat yang ada stok di rak
        $obats = Obat::withSum('batches as stok_rak_total', 'stok_rak')
            ->having('stok_rak_total', '>', 0)
            ->orderBy('nama_obat')
            ->get();

        $categories = $obats->pluck('kategori')->filter()->unique()->values();

        return view('pages.kasir.index', [
            'title'      => 'Kasir — Point of Sale',
            'obats'      => $obats,
            'categories' => $categories,
        ]);
    }

    /**
     * Proses checkout — terima cart dari form, jalankan StokService
     */
    public function store(Request $request)
    {
        $request->validate([
            'items'               => 'required|array|min:1',
            'items.*.obat_id'     => 'required|exists:obat,id',
            'items.*.jumlah'      => 'required|integer|min:1',
            'nominal_bayar'       => 'required|numeric|min:0',
            'nama_pembeli'        => 'nullable|string|max:100',
            'catatan'             => 'nullable|string|max:255',
        ]);

        // Hitung total harga
        $totalHarga = 0;
        foreach ($request->items as $item) {
            $obat        = Obat::find($item['obat_id']);
            $totalHarga += $obat->harga * $item['jumlah'];
        }

        $nominalBayar = (float) $request->nominal_bayar;
        $kembalian    = $nominalBayar - $totalHarga;

        if ($kembalian < 0) {
            return back()->with('error', 'Nominal bayar kurang dari total harga.')->withInput();
        }

        try {
            $penjualan = $this->stokService->prosesPenjualan(
                dataTransaksi: [
                    'total_harga'   => $totalHarga,
                    'nominal_bayar' => $nominalBayar,
                    'kembalian'     => $kembalian,
                    'nama_pembeli'  => $request->nama_pembeli,
                    'catatan'       => $request->catatan,
                ],
                items:  $request->items,
                userId: auth()->id(),
            );

            return redirect()->route('kasir.index')
                ->with('success', "Transaksi {$penjualan->no_transaksi} berhasil diproses!")
                ->with('struk_id', $penjualan->id);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Halaman struk setelah transaksi sukses
     */
    public function struk(Penjualan $penjualan)
    {
        $penjualan->load(['details.obat', 'details.obatBatch', 'user']);

        return view('pages.kasir.struk', [
            'title'     => 'Struk Penjualan',
            'penjualan' => $penjualan,
        ]);
    }

    /**
     * Riwayat semua transaksi penjualan
     */
    public function riwayat(Request $request)
    {
        $query = Penjualan::with(['user', 'details']);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('no_transaksi', 'like', "%{$search}%")
                  ->orWhere('nama_pembeli', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_transaksi', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_transaksi', '<=', $request->tanggal_sampai);
        }

        $penjualans = $query->latest('tanggal_transaksi')->paginate(15)->withQueryString();

        $totalPendapatan = $query->sum('total_harga');

        return view('pages.kasir.riwayat', [
            'title'           => 'Riwayat Penjualan',
            'penjualans'      => $penjualans,
            'totalPendapatan' => $totalPendapatan,
        ]);
    }

    /**
     * Detail transaksi penjualan
     */
    public function show(Penjualan $penjualan)
    {
        $penjualan->load(['details.obat', 'details.obatBatch', 'user']);

        return view('pages.kasir.show', [
            'title'     => 'Detail Transaksi',
            'penjualan' => $penjualan,
        ]);
    }
}
