<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ObatController;
use App\Http\Controllers\ObatMasukController;
use App\Http\Controllers\TransferRakController;
use App\Http\Controllers\DisplayRakController;
use App\Http\Controllers\StokGudangController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PenggunaController;

// ── Autentikasi ──
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ── Redirect root ke dashboard ──
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// ── Rute yang memerlukan autentikasi ──
Route::middleware(['auth'])->group(function () {

    // Dashboard (semua role)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ─────────────────────────────────────────────────────────────────
    // 1. SEMUA ROLE (admin, karyawan, owner)
    // ─────────────────────────────────────────────────────────────────
    Route::middleware(['role:admin,karyawan,owner'])->group(function () {

        // Manajemen User
        Route::get('/supplier', [SupplierController::class, 'index'])->name('supplier.index');

        // Display Rak — lihat status stok rak semua obat
        Route::get('/display-rak', [DisplayRakController::class, 'index'])->name('display-rak.index');

        // Cetak Struk (semua role untuk keperluan bukti transaksi)
        Route::get('/kasir/struk/{penjualan}', [KasirController::class, 'struk'])->name('kasir.struk');

        // Riwayat Penjualan
        Route::get('/riwayat-penjualan', [KasirController::class, 'riwayat'])->name('riwayat-penjualan.index');
        Route::get('/riwayat-penjualan/{penjualan}', [KasirController::class, 'show'])->name('riwayat-penjualan.show');

        // Data Obat (lihat katalog & detail obat)
        Route::get('/obat', [ObatController::class, 'index'])->name('obat.index');
        // Route::get('/obat/{obat}', [ObatController::class, 'show'])->whereNumber('obat')->name('obat.show');

        // Stok Gudang (lihat stok gudang)
        Route::get('/stok-gudang', [StokGudangController::class, 'index'])->name('stok-gudang.index');

        // Obat Masuk — input penerimaan obat ke gudang
        Route::get('/obat-masuk/generate-batch', [ObatMasukController::class, 'generateBatchNumber'])->name('obat-masuk.generate-batch');
        Route::resource('obat-masuk', ObatMasukController::class)->only(['index', 'create', 'store', 'show']);
    });

    // ─────────────────────────────────────────────────────────────────
    // 2. KHUSUS ADMIN & KARYAWAN (Transaksi Kasir POS)
    // ─────────────────────────────────────────────────────────────────
    Route::middleware(['role:admin,karyawan'])->group(function () {

        // Manajemen Supplier
        Route::post('/supplier/create', [SupplierController::class, 'store']);
        Route::get('/supplier/create', [SupplierController::class, 'create'])->name('supplier.create');
        Route::get('/supplier/{supplier}', [SupplierController::class, 'show'])->whereNumber('supplier')->name('supplier.show');
        Route::get('/supplier/{supplier}/edit', [SupplierController::class, 'edit'])->whereNumber('supplier')->name('supplier.edit');
        Route::put('/supplier/{supplier}', [SupplierController::class, 'update'])->whereNumber('supplier')->name('supplier.update');
        Route::delete('/supplier/{supplier}', [SupplierController::class, 'destroy'])->whereNumber('supplier')->name('supplier.destroy');

        // Kasir
        Route::get('/kasir', [KasirController::class, 'index'])->name('kasir.index');
        Route::post('/kasir', [KasirController::class, 'store'])->name('kasir.store');

         // Transfer ke Rak — pindahkan stok dari gudang ke display rak (FEFO)
        Route::get('/transfer-rak', [TransferRakController::class, 'index'])->name('transfer-rak.index');
        Route::get('/transfer-rak/create', [TransferRakController::class, 'create'])->name('transfer-rak.create');
        Route::post('/transfer-rak', [TransferRakController::class, 'store'])->name('transfer-rak.store');
    });

    // ─────────────────────────────────────────────────────────────────
    // 2. KHUSUS ADMIN & OWNER (Master Data, ROP, Laporan, Pengguna)
    // ─────────────────────────────────────────────────────────────────
    Route::middleware(['role:admin,owner'])->group(function () {

        // Pemesanan (ROP)
        Route::resource('pesanan', PesananController::class)->except(['edit', 'update']);
        Route::patch('/pesanan/{pesanan}/status', [PesananController::class, 'updateStatus'])->name('pesanan.updateStatus');

        // Laporan
        Route::get('/laporan/obat-masuk', [LaporanController::class, 'obatMasuk'])->name('laporan.obat-masuk');
        Route::get('/laporan/obat-masuk/pdf', [LaporanController::class, 'obatMasukPdf'])->name('laporan.obat-masuk.pdf');
        Route::get('/laporan/obat-keluar', [LaporanController::class, 'obatKeluar'])->name('laporan.obat-keluar');
        Route::get('/laporan/obat-keluar/pdf', [LaporanController::class, 'obatKeluarPdf'])->name('laporan.obat-keluar.pdf');
        Route::get('/laporan/stok-obat', [LaporanController::class, 'stokObat'])->name('laporan.stok-obat');
        Route::get('/laporan/penjualan', [LaporanController::class, 'penjualan'])->name('laporan.penjualan');
    });

    // ─────────────────────────────────────────────────────────────────
    // 4. KHUSUS ADMIN
    // ─────────────────────────────────────────────────────────────────
    Route::middleware(['role:admin'])->group(function () {

        // Manajemen user
        Route::resource('pengguna', PenggunaController::class);

        // Manajemen Master Data Obat
        Route::get('/obat/create', [ObatController::class, 'create'])->name('obat.create');
        Route::post('/obat', [ObatController::class, 'store'])->name('obat.store');
        Route::get('/obat/{obat}/edit', [ObatController::class, 'edit'])->whereNumber('obat')->name('obat.edit');
        Route::put('/obat/{obat}', [ObatController::class, 'update'])->whereNumber('obat')->name('obat.update');
        Route::delete('/obat/{obat}', [ObatController::class, 'destroy'])->whereNumber('obat')->name('obat.destroy');
    });
});

// ── Error pages ──
Route::get('/error-404', function () {
    return view('pages.errors.error-404', ['title' => 'Error 404']);
})->name('error-404');
