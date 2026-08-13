<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ObatController;
use App\Http\Controllers\ObatMasukController;
use App\Http\Controllers\ObatKeluarController;
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

    // ── Rute untuk admin & karyawan ──
    Route::middleware(['role:admin,karyawan'])->group(function () {
        // Data Obat (lihat saja untuk karyawan)
        Route::get('/obat', [ObatController::class, 'index'])->name('obat.index');
        Route::get('/obat/{obat}', [ObatController::class, 'show'])->name('obat.show');

        // Obat Masuk (Batch)
        Route::resource('obat-masuk', ObatMasukController::class)->only(['index', 'create', 'store', 'show']);

        // Obat Keluar (FEFO)
        Route::resource('obat-keluar', ObatKeluarController::class)->only(['index', 'create', 'store']);
    });

    // ── Rute khusus admin ──
    Route::middleware(['role:admin'])->group(function () {
        // CRUD Obat (create, edit, delete hanya admin)
        Route::get('/obat/create', [ObatController::class, 'create'])->name('obat.create');
        Route::post('/obat', [ObatController::class, 'store'])->name('obat.store');
        Route::get('/obat/{obat}/edit', [ObatController::class, 'edit'])->name('obat.edit');
        Route::put('/obat/{obat}', [ObatController::class, 'update'])->name('obat.update');
        Route::delete('/obat/{obat}', [ObatController::class, 'destroy'])->name('obat.destroy');

        // Supplier
        Route::resource('supplier', SupplierController::class);

        // Pesanan
        Route::resource('pesanan', PesananController::class)->except(['edit', 'update']);
        Route::patch('/pesanan/{pesanan}/status', [PesananController::class, 'updateStatus'])->name('pesanan.updateStatus');

        // Laporan
        Route::get('/laporan/obat-masuk', [LaporanController::class, 'obatMasuk'])->name('laporan.obat-masuk');
        Route::get('/laporan/obat-keluar', [LaporanController::class, 'obatKeluar'])->name('laporan.obat-keluar');
        Route::get('/laporan/obat-masuk/pdf', [LaporanController::class, 'obatMasukPdf'])->name('laporan.obat-masuk.pdf');
        Route::get('/laporan/obat-keluar/pdf', [LaporanController::class, 'obatKeluarPdf'])->name('laporan.obat-keluar.pdf');

        // Pengguna
        Route::resource('pengguna', PenggunaController::class);
    });
});

// ── Error pages ──
Route::get('/error-404', function () {
    return view('pages.errors.error-404', ['title' => 'Error 404']);
})->name('error-404');
