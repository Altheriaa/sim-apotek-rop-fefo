# Perancangan Sistem Informasi Manajemen Stok Obat
### Studi Kasus: Apotek Tabah Farma — Implementasi Laravel + FEFO + ROP + Fonnte

---

## 1. Arsitektur & Tech Stack

| Komponen | Pilihan | Catatan |
|---|---|---|
| Framework | Laravel 11 | MVC, Eloquent ORM |
| Database | MySQL | via XAMPP/Laragon saat development |
| Autentikasi | Laravel Breeze / Fortify | role-based (admin, karyawan) |
| Notifikasi WA | Fonnte API | via queue job async |
| Frontend | Blade + Bootstrap/Tailwind | sesuai kebutuhan skripsi (Bootstrap agar selaras proposal awal) |
| Scheduler | Laravel Task Scheduling | cek ROP & ED harian |
| Queue | Database/Redis driver | kirim notifikasi WA tanpa blocking request |
| Testing | PHPUnit + BlackBox manual | sesuai batasan masalah proposal |

---

## 2. Perubahan Skema dari Proposal Awal (dan Alasannya)

| Proposal awal | Revisi | Alasan |
|---|---|---|
| `tanggal_kadaluwarsa` di tabel `obat` | Dipindah ke tabel baru `obat_batch` | Satu obat bisa punya banyak kiriman dengan ED berbeda — FEFO baru valid bila dilacak per batch |
| `stok` sebagai kolom di `obat` | Dihitung (`SUM(stok_sisa)` dari `obat_batch`) | Menghindari data stok "ganda sumber kebenaran" yang gampang tidak sinkron |
| `notifikasi` tanpa status kirim | Tambah `target_nomor`, `status`, `fonnte_id`, `dikirim_at` | Diperlukan untuk melacak keberhasilan pengiriman WA via Fonnte |
| Table Pemesanan tanpa detail item | Dipecah jadi `pesanan` + `detail_pesanan` | Satu pesanan ke supplier bisa berisi banyak obat sekaligus |

---

## 3. Skema Database (Migration Laravel)

```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('nama_user');
    $table->string('username')->unique();
    $table->string('password');
    $table->enum('role', ['admin', 'karyawan']);
    $table->timestamps();
});

Schema::create('supplier', function (Blueprint $table) {
    $table->id();
    $table->string('nama_supplier');
    $table->text('alamat')->nullable();
    $table->string('kontak')->nullable();
    $table->timestamps();
});

Schema::create('obat', function (Blueprint $table) {
    $table->id();
    $table->string('nama_obat');
    $table->string('satuan');
    $table->integer('rop_minimum')->default(0);
    $table->timestamps();
});

Schema::create('obat_batch', function (Blueprint $table) {
    $table->id();
    $table->foreignId('obat_id')->constrained('obat');
    $table->foreignId('supplier_id')->nullable()->constrained('supplier');
    $table->string('no_batch')->nullable();
    $table->date('tanggal_masuk');
    $table->date('tanggal_kadaluwarsa');
    $table->integer('stok_awal');
    $table->integer('stok_sisa');
    $table->timestamps();
});

Schema::create('obat_keluar', function (Blueprint $table) {
    $table->id();
    $table->foreignId('obat_id')->constrained('obat');
    $table->foreignId('obat_batch_id')->constrained('obat_batch');
    $table->foreignId('user_id')->constrained('users');
    $table->date('tanggal_keluar');
    $table->integer('jumlah');
    $table->timestamps();
});

Schema::create('pesanan', function (Blueprint $table) {
    $table->id();
    $table->foreignId('supplier_id')->constrained('supplier');
    $table->foreignId('user_id')->nullable()->constrained('users');
    $table->date('tanggal_pesan');
    $table->string('status')->default('draft'); // draft, diproses, dikirim, selesai
    $table->timestamps();
});

Schema::create('detail_pesanan', function (Blueprint $table) {
    $table->id();
    $table->foreignId('pesanan_id')->constrained('pesanan')->cascadeOnDelete();
    $table->foreignId('obat_id')->constrained('obat');
    $table->integer('jumlah_pesan');
    $table->timestamps();
});

Schema::create('notifikasi', function (Blueprint $table) {
    $table->id();
    $table->foreignId('obat_id')->nullable()->constrained('obat');
    $table->string('jenis_notifikasi'); // stok_menipis | mendekati_kadaluwarsa
    $table->text('pesan');
    $table->string('target_nomor');
    $table->string('status')->default('pending'); // pending | terkirim | gagal
    $table->string('fonnte_id')->nullable();
    $table->timestamp('dikirim_at')->nullable();
    $table->timestamps();
});
```

---

## 4. Struktur Folder Laravel yang Disarankan

```
app/
├── Console/
│   └── Kernel.php                 # scheduler: cek ROP & ED harian
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   ├── ObatController.php
│   │   │   ├── SupplierController.php
│   │   │   ├── ROPController.php
│   │   │   ├── PesananController.php
│   │   │   ├── LaporanController.php
│   │   │   └── PenggunaController.php
│   │   └── ObatMasukController.php    # shared admin & karyawan
│   │   └── ObatKeluarController.php   # shared admin & karyawan
│   └── Middleware/
│       └── CheckRole.php              # middleware role admin/karyawan
├── Models/
│   ├── User.php
│   ├── Supplier.php
│   ├── Obat.php
│   ├── ObatBatch.php
│   ├── ObatKeluar.php
│   ├── Pesanan.php
│   ├── DetailPesanan.php
│   └── Notifikasi.php
├── Services/
│   ├── StokService.php            # logika FEFO + cek ROP
│   └── FonnteService.php          # kirim WA
└── Jobs/
    └── KirimNotifikasiWhatsapp.php
```

---

## 5. Hak Akses (Role Middleware)

```php
// routes/web.php
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('rop', ROPController::class);
    Route::resource('pengguna', PenggunaController::class);
    Route::resource('pesanan', PesananController::class);
    Route::get('laporan/obat-masuk', [LaporanController::class, 'obatMasuk']);
    Route::get('laporan/obat-keluar', [LaporanController::class, 'obatKeluar']);
});

Route::middleware(['auth', 'role:admin,karyawan'])->group(function () {
    Route::resource('obat', ObatController::class)->only(['index', 'show']);
    Route::resource('obat-masuk', ObatMasukController::class);
    Route::resource('obat-keluar', ObatKeluarController::class);
    Route::get('stok', [StokController::class, 'index']);
});
```

```php
// app/Http/Middleware/CheckRole.php
public function handle(Request $request, Closure $next, ...$roles)
{
    if (!in_array($request->user()->role, $roles)) {
        abort(403, 'Akses ditolak.');
    }
    return $next($request);
}
```

---

## 6. Alur Sistem

### 6.1 Autentikasi & Routing berdasarkan Role
1. User membuka halaman login → input username & password.
2. Laravel Breeze validasi kredensial.
3. Setelah login, redirect ke dashboard sesuai role:
   - `admin` → dashboard lengkap (statistik stok, notifikasi, laporan)
   - `karyawan` → dashboard operasional (obat masuk/keluar, cari obat)
4. Middleware `role` menolak akses ke rute yang tidak diizinkan (mis. karyawan akses `/rop` → 403).

### 6.2 Obat Masuk (Batch Baru)
1. Admin/karyawan pilih menu "Obat Masuk".
2. Input: obat, supplier, no batch, tanggal masuk, tanggal ED, jumlah.
3. Sistem membuat record baru di `obat_batch` (bukan update stok lama).
4. `stok_total` obat otomatis bertambah karena dihitung dari `SUM(stok_sisa)`.
5. Jika ini penerimaan dari pesanan yang berstatus "dikirim", admin bisa hubungkan ke `pesanan_id` agar status berubah otomatis jadi "selesai".

### 6.3 Obat Keluar (Algoritma FEFO)
1. User pilih menu "Obat Keluar", sistem tampilkan daftar obat dengan ringkasan stok.
2. User pilih obat & input jumlah keluar.
3. `StokService::keluarkanObat()`:
   - Ambil semua batch obat tsb yang `stok_sisa > 0`, urut `tanggal_kadaluwarsa ASC` (FEFO).
   - Kurangi `stok_sisa` mulai dari batch ED terdekat; jika jumlah keluar melebihi 1 batch, lanjut ke batch berikutnya.
   - Simpan record di `obat_keluar` per batch yang terpakai (transaksi bisa split ke >1 baris jika lintas batch).
4. Setelah update stok, panggil `cekRop()` — jika `stok_total <= rop_minimum`, buat notifikasi otomatis.

### 6.4 Pengecekan ROP & Notifikasi Otomatis
1. Dipicu di dua tempat:
   - Setelah setiap transaksi obat keluar (real-time).
   - Terjadwal harian via Laravel Scheduler (`schedule->call(...)->daily()`), untuk menangkap kasus stok yang menipis tanpa transaksi baru.
2. Jika `stok_total <= rop_minimum` → buat record `notifikasi` (`jenis: stok_menipis`) → dispatch `KirimNotifikasiWhatsapp` job.
3. Scheduler harian juga mengecek `obat_batch` dengan `tanggal_kadaluwarsa` dalam 30 hari ke depan → buat notifikasi `mendekati_kadaluwarsa`.
4. Job queue mengirim pesan via Fonnte API (`POST https://api.fonnte.com/send`), lalu update `status` (`terkirim`/`gagal`) dan `fonnte_id` di tabel `notifikasi`.

### 6.5 Pemesanan Ulang ke Supplier
1. Notifikasi stok kritis memicu pembuatan draft `pesanan` (status: `draft`) beserta `detail_pesanan`.
2. Admin membuka halaman "Data Pesanan", review & edit jumlah, pilih/ubah supplier.
3. Admin approve → status berubah jadi `diproses`.
4. Pemesanan riil dilakukan di luar sistem (telepon/WA manual ke supplier) — admin update status jadi `dikirim` setelah konfirmasi supplier.
5. Saat obat fisik tiba, dicatat via alur 6.2 (Obat Masuk), dihubungkan ke `pesanan_id` → status otomatis `selesai`.

### 6.6 Laporan
1. Admin mengakses menu Laporan Obat Masuk / Obat Keluar.
2. Filter berdasarkan rentang tanggal, obat, atau supplier.
3. Data diambil dari `obat_batch` (untuk masuk) dan `obat_keluar` (untuk keluar), dengan agregasi per obat/per periode.
4. Opsional: export ke PDF/Excel (bisa pakai package `barryvdh/laravel-dompdf` atau `maatwebsite/excel`).

---

## 7. Contoh Service Class Inti

```php
class StokService
{
    public function keluarkanObat(int $obatId, int $jumlah, int $userId): array
    {
        $obat = Obat::findOrFail($obatId);
        if ($obat->stok_total < $jumlah) {
            throw new \Exception('Stok tidak mencukupi.');
        }

        $sisa = $jumlah;
        $batchTerpakai = [];

        DB::transaction(function () use ($obat, &$sisa, $userId, &$batchTerpakai) {
            $batches = ObatBatch::where('obat_id', $obat->id)
                ->where('stok_sisa', '>', 0)
                ->orderBy('tanggal_kadaluwarsa')
                ->lockForUpdate()
                ->get();

            foreach ($batches as $batch) {
                if ($sisa <= 0) break;
                $ambil = min($batch->stok_sisa, $sisa);
                $batch->decrement('stok_sisa', $ambil);

                ObatKeluar::create([
                    'obat_id' => $obat->id,
                    'obat_batch_id' => $batch->id,
                    'user_id' => $userId,
                    'tanggal_keluar' => now(),
                    'jumlah' => $ambil,
                ]);

                $batchTerpakai[] = ['batch' => $batch->no_batch, 'jumlah' => $ambil];
                $sisa -= $ambil;
            }
        });

        $this->cekRop($obat->fresh());
        return $batchTerpakai;
    }

    public function cekRop(Obat $obat): void
    {
        if ($obat->stok_total <= $obat->rop_minimum) {
            $notif = Notifikasi::create([
                'obat_id' => $obat->id,
                'jenis_notifikasi' => 'stok_menipis',
                'pesan' => "Stok {$obat->nama_obat} tersisa {$obat->stok_total}, di bawah ROP ({$obat->rop_minimum}).",
                'target_nomor' => config('services.fonnte.admin_target'),
            ]);
            KirimNotifikasiWhatsapp::dispatch($notif);
        }
    }
}
```

```php
class FonnteService
{
    public function kirim(string $target, string $pesan): array
    {
        $response = Http::withHeaders(['Authorization' => config('services.fonnte.token')])
            ->asForm()
            ->post('https://api.fonnte.com/send', [
                'target' => $target,
                'message' => $pesan,
                'countryCode' => '62',
            ]);
        return $response->json();
    }
}
```

---

## 8. Rencana Pengujian (BlackBox Testing)

Sesuai batasan masalah proposal, pengujian fungsional menggunakan metode BlackBox. Contoh skenario uji:

| No | Skenario | Input | Hasil yang Diharapkan |
|---|---|---|---|
| 1 | Login admin valid | username & password benar | Redirect ke dashboard admin |
| 2 | Login gagal | password salah | Pesan error, tetap di halaman login |
| 3 | Karyawan akses menu ROP | akses `/rop` sebagai karyawan | Ditolak (403) |
| 4 | Obat keluar lintas batch | jumlah keluar > stok batch pertama | Sistem ambil dari 2 batch, urut ED terdekat |
| 5 | Obat keluar stok tidak cukup | jumlah keluar > stok total | Muncul pesan error, transaksi tidak tersimpan |
| 6 | Stok di bawah ROP | transaksi keluar bikin stok ≤ ROP | Notifikasi dibuat & WA terkirim ke admin |
| 7 | Obat mendekati ED | batch dengan ED < 30 hari | Notifikasi `mendekati_kadaluwarsa` muncul |
| 8 | Approve pesanan | admin approve draft pesanan | Status `pesanan` berubah jadi `diproses` |
| 9 | Terima obat dari pesanan | input obat masuk terhubung `pesanan_id` | Status pesanan otomatis `selesai` |

---

## 9. Catatan untuk Penyesuaian Dokumen Skripsi

1. Update Gambar 3.8 (ERD) di proposal dengan versi final di atas.
2. Tambahkan sub-bab **2.12 Fonnte** di Tinjauan Pustaka.
3. Perbarui Tabel 3.3–3.8 agar sesuai kolom baru (`obat_batch`, `pesanan` + `detail_pesanan`, `notifikasi` dengan kolom Fonnte).
4. Sebutkan Laravel secara eksplisit di 3.4 Kebutuhan Perangkat Lunak (proposal awal masih menyebut CodeIgniter).
5. Hapus penyebutan DFD/Class Diagram di 3.5 bila tidak dibuat, atau tambahkan sebagai lampiran.
